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

        /**
         * Page numbers to actually render in a pagination control, instead
         * of printing every page 1..N (unreadable once there are 15-20+
         * pages). Always keeps the first and last page, plus a small window
         * around the current page; gaps are represented as null, which the
         * view renders as a "..." (non-clickable) gap marker.
         *
         * e.g. current=6, total=21, delta=1 -> [1, null, 5, 6, 7, null, 21]
         */
        public static function pageWindow($currentPage, $totalPages, $delta = 1){
            $currentPage = max(1, (int) $currentPage);
            $totalPages = max(1, (int) $totalPages);

            $keep = [];
            for($i = 1; $i <= $totalPages; $i++){
                if($i === 1 || $i === $totalPages || ($i >= $currentPage - $delta && $i <= $currentPage + $delta)){
                    $keep[] = $i;
                }
            }

            $window = [];
            $previous = null;
            foreach($keep as $page){
                if($previous !== null && $page - $previous > 1){
                    $window[] = null;
                }
                $window[] = $page;
                $previous = $page;
            }
            return $window;
        }
    }
