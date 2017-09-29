<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class StoreEvent
{
    /**
     * @var EventDispatcher
     */
    static private $event;

    /**
     * StoreEvent constructor.
     * @author Joachim Doerr
     */
    public function __construct()
    {
        self::$event = new EventDispatcher();
    }

    /**
     * @return EventDispatcher
     * @author Joachim Doerr
     */
    static public function dispatcher()
    {
        return self::$event;
    }

    /**
     * @param $eventName
     * @param Event|null $event
     * @return Event
     * @author Joachim Doerr
     */
    static public function dispatch($eventName, Event $event = null)
    {
        return self::$event->dispatch($eventName, $event);
    }

    /**
     * @param string $eventName
     * @param callable $listener
     * @param int $priority
     * @author Joachim Doerr
     */
    static public function addListener($eventName, $listener, $priority = 0)
    {
        self::$event->addListener($eventName, $listener, $priority);
    }
}