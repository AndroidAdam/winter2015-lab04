<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        $CI = &get_instance();
        $order = $this->orders->get($num);
        
        if($CI->orderitems->exists($num, $code))
        {
            $record = $CI->orderitems->get($num, $code);
            $record->quantity++;
            $CI->orderitems->update($record);
        }
        else
        {
            $record = $CI->orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->orderitems->add($record);
        }
        
        $order->total = $this->total($num);
        $ci->orders->update($order);
        
    }
    // calculate the total for an order
    function total($num) {
        $this->load->model('orderitems');
        $items = $this->orderitems->some('order', $num);
        $total = 0.0;
        
        // Sum the price for each kind of menu item.
        foreach ($items as $item) {
            $menuItem = $this->menu->get($item->item);
            $total += ($menuItem->price * $item->quantity);
        }
        
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        $CI = &get_instance();
        $items = $CI->orderitems->group($num);
        $chosen = array();
        
        // Create menu categories array
        if (count($items) > 0) {
            foreach($items as $item) {
                $menu = $CI->menu->get($item->item);
                $chosen[$menu->category] = 1;
            }
        }
        
        return (isset($chosen['m']) && isset($chosen['d']) && isset($chosen['s']));

}
}
