<?php

    function formatDateTime($date){
        return date('d M Y, h: i A', strtotime($date));
    }

    function formatDateShort($date){
        return date('d M Y', strtotime($date));
    }

    function formatStatusBadge($status){
        $badge = [
            'completed' => 'bg-green-color text-white',
            'cancelled' => 'bg-red-color text-white'
        ];

        return $badge($status) ?? 'bg-yellow-color text-gray-700';
    }

    function formatPrice($price){
        return '$'. number_format((float)$price, 2);
    }

    function timeAgo($datetime){
        $timestamp = strtotime($datetime);
        $diff = time() .$timestamp;

        if($diff<60) return $diff. 's ago';

        if($diff<3600) return floor($diff/60). 'm ago';

        if($diff<86400) return floor($diff/3600). 'h ago';

        if($diff<604800) return floor ($diff/86400). 'd ago';

        return date('d M', $timestamp);
    }


?>