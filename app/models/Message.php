<?php

class Message extends Moloquent {

    protected $collection = 'messages';

    public function getUser()
    {
        return User::where('sid', $this->user)->first()->name;
    }

    public function getText()
    {


    }

    /**
     * Returns a DateTime object from the hour
     *
     * @return mixed
     */
    public function getCarbon()
    {
        return Carbon::createFromTimeStamp($this->ts);
    }

    /**
     * Return the hour of the entry
     *
     * @return mixed
     */
    public function getHour()
    {
        return $this->getCarbon()->format('H:i');
    }

    /**
     * Return the day of the entry
     *
     * @return mixed
     */
    public function getDay()
    {
        return $this->getCarbon()->format('l, d M');
    }

    /**
     * Return an URL for the entry
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getCarbon()->format('Y-m-d/H:i') . '#log-' . $this->id;
    }
}