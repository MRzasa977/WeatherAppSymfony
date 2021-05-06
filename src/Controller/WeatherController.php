<?php

namespace App\Controller;

use App\Entity\Cities;
use App\Entity\Weather;
use App\Repository\CitiesRepository;
use App\Repository\WeatherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="weather")
 */
class WeatherController extends AbstractController
{
    /**
     * @Route("/", name=".create")
     */
    public function create(Request $request, CitiesRepository $citiesRepository, WeatherRepository $weatherRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('location', TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();

            try {
                $file = file_get_contents('http://api.openweathermap.org/data/2.5/weather?q=' . $data['location'] . '&units=metric&appid=0a9297efc9b7b6903bd5b48f5ff814ef');
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'City not found!'
                );
                return $this->redirectToRoute('weather.create');
            }
            $obj = json_decode($file);
            $city = $citiesRepository->findOneBy(array('city' => $obj->name));
            $dateTime = new \DateTime(date('Y-m-d H:i:s', $obj->dt));
            if (!$city) {
                $city = new Cities();
                $city->setCity($obj->name);
                $city->setLat($obj->coord->lat);
                $city->setLon($obj->coord->lon);
                $currentWeather = new Weather();
                return $this->createWeather($city, $currentWeather, $dateTime, $obj);
            } else if (!$weatherRepository->findByDate($dateTime, $city->getId())) {
                $currentWeather = new Weather();
                return $this->createWeather($city, $currentWeather, $dateTime, $obj);
            } else {
                return $this->redirectToRoute('weather.show', [
                    'id' => $city->getId()
                ]);
            }
        }

        return $this->render('weather/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{id}", name=".show")
     */
    public function show(Cities $city): Response
    {
        $data = $city;
        $weather = $city->getWeather()->getValues();
        $currentWeather = $weather[0];
        array_shift($weather);
        return $this->render('weather/show.html.twig', [
            'city' => $data,
            'current_weather' => $currentWeather,
            'weather' => $weather
        ]);
    }

    public function createWeather(Cities $city, Weather $currentWeather, \DateTime $dateTime, $obj): Response
    {
        $currentWeather->setTemp($obj->main->temp);
        $currentWeather->setTempFeel($obj->main->feels_like);
        $currentWeather->setTempMax($obj->main->temp_max);
        $currentWeather->setTempMin($obj->main->temp_min);
        $currentWeather->setDate($dateTime);
        $currentWeather->setWeather($obj->weather[0]->main);
        $city->addWeather($currentWeather);
        $em = $this->getDoctrine()->getManager();
        $em->persist($currentWeather);
        $em->persist($city);
        $em->flush();
        return $this->redirectToRoute('weather.show', [
            'id' => $city->getId()
        ]);
    }

}
