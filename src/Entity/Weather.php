<?php

namespace App\Entity;

use App\Repository\WeatherRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WeatherRepository::class)
 */
class Weather
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $temp;

    /**
     * @ORM\Column(type="float")
     */
    private $temp_min;

    /**
     * @ORM\Column(type="float")
     */
    private $temp_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $temp_feel;


    /**
     * @ORM\ManyToOne(targetEntity=Cities::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $cities;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $weather;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemp(): ?float
    {
        return $this->temp;
    }

    public function setTemp(float $temp): self
    {
        $this->temp = $temp;

        return $this;
    }

    public function getTempMin(): ?float
    {
        return $this->temp_min;
    }

    public function setTempMin(float $temp_min): self
    {
        $this->temp_min = $temp_min;

        return $this;
    }

    public function getTempMax(): ?float
    {
        return $this->temp_max;
    }

    public function setTempMax(float $temp_max): self
    {
        $this->temp_max = $temp_max;

        return $this;
    }

    public function getTempFeel(): ?float
    {
        return $this->temp_feel;
    }

    public function setTempFeel(?float $temp_feel): self
    {
        $this->temp_feel = $temp_feel;

        return $this;
    }


    public function getCities(): ?Cities
    {
        return $this->cities;
    }

    public function setCities(?Cities $cities): self
    {
        $this->cities = $cities;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(string $weather): self
    {
        $this->weather = $weather;

        return $this;
    }
}
