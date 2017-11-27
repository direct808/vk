<?php
namespace Direct808\Vk;


interface QueryEngine
{
    public function query($url, array $parameters = []);
}