<?php

    class Paginator{
        public static function offset($page, $perPage){
            return (max(1, (int) $page) - 1) * $perPage;
        }

        public static function meta($total, $page, $perPage){
            $page = max(1, (int) $page);
            return [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => max(1, (int) ceil($total / $perPage)),
            ];
        }
    }
