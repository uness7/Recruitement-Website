<?php

namespace App\Controller\Queries;

class HomepageSearchData
{
    private string $title;


    public function __construct($title)
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }



}