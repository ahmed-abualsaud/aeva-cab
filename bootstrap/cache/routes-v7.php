<?php

/*
|--------------------------------------------------------------------------
| Load The Cached Routes
|--------------------------------------------------------------------------
|
| Here we will decode and unserialize the RouteCollection instance that
| holds all of the route information for an application. This allows
| us to instantaneously load the entire route map into the router.
|
*/

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/websockets' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zu1lRFpBySSLcw7Y',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/websockets/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pRhWRVd1WZvPpSzJ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/websockets/event' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::BX4EaVeKpaeIvduG',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/websockets/statistics' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Df2SPcLhvp3eJqPK',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/stats' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.stats.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/workload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.workload.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/masters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.masters.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/monitoring' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.monitoring.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.monitoring.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/metrics/jobs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.jobs-metrics.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/metrics/queues' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.queues-metrics.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/batches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.jobs-batches.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/jobs/pending' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.pending-jobs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/jobs/completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.completed-jobs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/horizon/api/jobs/failed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.failed-jobs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graphql-playground' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graphql-playground',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graphql' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graphql',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'POST' => 1,
            'HEAD' => 2,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Wb4S6kotkGbdriMU',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/exceptions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZELAfFBHoeRQrwB3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/dumps' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::l6qUEwbpsUsGvgTB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1CaEmQtHFyQCDh5B',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::P9tOgd6iYw0oVjCi',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/jobs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NaZjx6jpNy8blDIu',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/batches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MDHzn2IBbSBmAogm',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/events' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ttvEQ4eEzFhPCdTB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/gates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LFGQnmE2ymfexa1M',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/cache' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fg75nwXey7xqKSth',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/queries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0iO0CzBh5795z9yj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/models' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GdPQGNP3cXxmF5VL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/requests' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5P5XVQ6EXjVEyWAk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/views' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UYhHOd9j4hXCaT74',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/commands' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gl0SyYyL0SmgcIH2',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/schedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::D96FhI341rmPlKR6',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/redis' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PsoP3mulh6YZBZHC',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/client-requests' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YxkORXeFF1XAtrxf',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/monitored-tags' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ayVXJVphugrb6mue',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aMQzcn76hvocD9MU',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/monitored-tags/delete' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yobXYqlhc3LNpy5M',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/toggle-recording' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QdopWbJLk4e5fXCR',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/telescope/telescope-api/entries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cXnq2HtRt2Tbr5ze',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/broadcasting/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::e1l9MKGVKn8Jjw3B',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'POST' => 1,
            'HEAD' => 2,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/admin/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::trhkrb6qICKrkWlg',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/driver/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6Oz4BlAjxcIUE8wj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/user/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gB16couXrZ4803BW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/user/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qnJvLIN5qmAAndXX',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/user/social/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dpLCUSqrp7opOFiM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/terminal/transaction' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OMTSu4uhheZSocNL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/user/avatar/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eH5Xse68K01W7fkA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/driver/avatar/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fvJuOHFFrNl5ibLc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/driver/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ro9oI4boGtC9NJVo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/driver/update/password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::z5LKlBd7xHCTqYBb',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/ready' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::53AaodVLB97TsMCB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/start' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::apOdVXxSRkc5jXjC',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/end' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::K6WuBJH9x129PQqJ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/at/station' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YVjHxfOLfejHqpEw',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/pick/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PLtRyO3S4uaI1xfV',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/drop/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::o0aaDtcqvZM3crUN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/update/driver/location' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JFl6YiO2c6GDEKm5',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/event/change/attendance/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IbIWlVLccxhOmjW7',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/ready' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GXH4n24wrbseqTBN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/start' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dJy5SwGri71lX0nh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/end' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HqIMfWcvhuavqqb3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/at/station' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CUkaiT0NNVXQcTMM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/pick/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::r51rWMALaOIbiYYa',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/drop/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::e4gZOA2uKda73YzD',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/event/update/driver/location' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GqP6nrdUwPsBNMAs',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/attendance/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sLMor9lyD7Tj9Z8i',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/send/message' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XwYnzialDhPETmvN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/booking/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::td9fA0K9u2mdGQWW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/pos/transaction' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hZVPaiJ0p1MW9ARU',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/pos/bulk-transaction' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UxbnU6ntqHywT8eW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/driver/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rfEElhRzRFSmyOg2',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/business/trip/private/chat/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::y0xPmx5bmv43jv8g',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/drivers/device/id' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9tHa5s67Il9nxmWf',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/terminal/transactions/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Vfy4JX6AUUS3s8wj',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rest/seats/trip/pos/transactions/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LVaqytBAKq7WA5zb',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HrximAro8VChtr0M',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/websockets/api/([^/]++)/statistics(*:42)|/horizon(?|/api/(?|m(?|onitoring/([^/]++)(?|(*:93))|etrics/(?|jobs/([^/]++)(*:124)|queues/([^/]++)(*:147)))|batches/(?|([^/]++)(*:176)|retry/([^/]++)(*:198))|jobs/(?|failed/([^/]++)(*:230)|retry/([^/]++)(*:252)|([^/]++)(*:268)))|(?:/((?:.*)))?(*:292))|/telescope(?|/telescope\\-api/(?|m(?|ail/([^/]++)(?|(*:352)|/(?|preview(*:371)|download(*:387)))|odels/([^/]++)(*:411))|e(?|xceptions/([^/]++)(?|(*:445))|vents/([^/]++)(*:468))|logs/([^/]++)(*:490)|notifications/([^/]++)(*:520)|jobs/([^/]++)(*:541)|batches/([^/]++)(*:565)|gates/([^/]++)(*:587)|c(?|ache/([^/]++)(*:612)|ommands/([^/]++)(*:636)|lient\\-requests/([^/]++)(*:668))|queries/([^/]++)(*:693)|re(?|quests/([^/]++)(*:721)|dis/([^/]++)(*:741))|views/([^/]++)(*:764)|schedule/([^/]++)(*:789))|(?:/((?:.*)))?(*:812))|/rest/(?|driver/([^/]++)(?|(*:848)|/(?|seats/trips(?|(*:874)|/schedule(*:891))|business/trips/schedule(*:923)|live/(?|business/trips(*:953)|seats/trips(*:972))|device/id(*:990)))|business/trip/(?|([^/]++)(?|(*:1028)|/(?|s(?|tations(*:1052)|ubscribers(*:1071))|attendance(*:1091)))|users/status(?:/([^/]++))?(*:1128)|([^/]++)/user/([^/]++)/status(*:1166))|seats/trip/(?|line/([^/]++)/stations(*:1212)|([^/]++)/(?|users(*:1238)|app/transactions/detail(*:1270))|pos/vehicle/([^/]++)/max\\-serial(*:1312))|user/([^/]++)/(?|business/trip/chat/messages(*:1366)|device/id(*:1384))|partner/([^/]++)/payment\\-categories(*:1430))|/([^/]++)/password/reset/([^/]+)\\?email\\=([^/]++)(*:1489))/?$}sDu',
    ),
    3 => 
    array (
      42 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ems7rznkGR3moapu',
          ),
          1 => 
          array (
            0 => 'appId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      93 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.monitoring-tag.paginate',
          ),
          1 => 
          array (
            0 => 'tag',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.monitoring-tag.destroy',
          ),
          1 => 
          array (
            0 => 'tag',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      124 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.jobs-metrics.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      147 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.queues-metrics.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      176 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.jobs-batches.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      198 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.jobs-batches.retry',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      230 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.failed-jobs.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      252 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.retry-jobs.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      268 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.jobs.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      292 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'horizon.index',
            'view' => NULL,
          ),
          1 => 
          array (
            0 => 'view',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      352 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::l0uBjZk0EO4Ds1Ds',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      371 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DYNuOUFK6rlUPKDU',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      387 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::I4jXmG62biLcDgd3',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      411 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MozA1OOoFltls87b',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      445 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9Nj3w7NwBeRvoTkX',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::S9wg1HmU28nuYsfw',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      468 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::G5aN8qGECkNv3RoO',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      490 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ARvplLWhINJ32fDS',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      520 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gRzLRSvfcnqwFxsF',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      541 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6gnYtPrIUD92U3Hu',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      565 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HJqjFuFn7CCJc3zg',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      587 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CIuF8B1PcvcKFawx',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      612 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nZpfaBkxiNd6uGhW',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      636 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::thHlRIGdTNxsOqJK',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      668 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CHYpSkhbPzg11VIM',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      693 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XeOA9QcMYZvTZsWW',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      721 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yeXe9bxTrG0Vm7mC',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      741 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::T5IKpS2LPeF832HC',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      764 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZB1biQCfBIOMQvHo',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      789 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aJh7WTC8Lt2ij85g',
          ),
          1 => 
          array (
            0 => 'telescopeEntryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      812 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'telescope',
            'view' => NULL,
          ),
          1 => 
          array (
            0 => 'view',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      848 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TfFlN1X3U6KlAMac',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      874 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rHQpizHJgbKZ3s6z',
          ),
          1 => 
          array (
            0 => 'driver_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      891 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ghj62yHIxQSCVknD',
          ),
          1 => 
          array (
            0 => 'driver_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      923 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PeIHjhayIiWLIvOg',
          ),
          1 => 
          array (
            0 => 'driver_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      953 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MrWly0C5pCuAN6V5',
          ),
          1 => 
          array (
            0 => 'driver_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      972 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nI7jCoM8DaHRjq4l',
          ),
          1 => 
          array (
            0 => 'driver_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      990 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IEGjHdG4lQEMraPv',
          ),
          1 => 
          array (
            0 => 'driver_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1028 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::t9pjl4DupBGkIqZt',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1052 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oDaIT21b1ENXdJBF',
          ),
          1 => 
          array (
            0 => 'trip_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1071 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::q4PH399CxGU34ZXt',
          ),
          1 => 
          array (
            0 => 'trip_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1091 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ON0faokFdMnswmm3',
          ),
          1 => 
          array (
            0 => 'trip_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1128 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ISvzK1oOggTypnkm',
            'trip_id' => NULL,
          ),
          1 => 
          array (
            0 => 'trip_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1166 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cAQeP24QolIzaRA6',
          ),
          1 => 
          array (
            0 => 'trip_id',
            1 => 'user_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1212 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KQ3c9fvEynLff9Qw',
          ),
          1 => 
          array (
            0 => 'line_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1238 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QYSLxaq18SlDzOCA',
          ),
          1 => 
          array (
            0 => 'trip_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1270 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UxK5cXIh7UtbwoOf',
          ),
          1 => 
          array (
            0 => 'trip_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1312 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lpunMfAVqFCM4YYV',
          ),
          1 => 
          array (
            0 => 'vehicle_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1366 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::l0KmkaMk0WIQeGd9',
          ),
          1 => 
          array (
            0 => 'user_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1384 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wfKYk3T6iaIABfd0',
          ),
          1 => 
          array (
            0 => 'user_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1430 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::N4HAKJcXXlDO28QR',
          ),
          1 => 
          array (
            0 => 'partner_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1489 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.reset',
          ),
          1 => 
          array (
            0 => 'type',
            1 => 'token',
            2 => 'email',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'generated::zu1lRFpBySSLcw7Y' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'websockets',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Middleware\\Authorize',
        ),
        'uses' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\ShowDashboard@__invoke',
        'controller' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\ShowDashboard',
        'namespace' => NULL,
        'prefix' => 'websockets',
        'where' => 
        array (
        ),
        'as' => 'generated::zu1lRFpBySSLcw7Y',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ems7rznkGR3moapu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'websockets/api/{appId}/statistics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Middleware\\Authorize',
        ),
        'uses' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\DashboardApiController@getStatistics',
        'controller' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\DashboardApiController@getStatistics',
        'namespace' => NULL,
        'prefix' => 'websockets',
        'where' => 
        array (
        ),
        'as' => 'generated::ems7rznkGR3moapu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pRhWRVd1WZvPpSzJ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'websockets/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Middleware\\Authorize',
        ),
        'uses' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\AuthenticateDashboard@__invoke',
        'controller' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\AuthenticateDashboard',
        'namespace' => NULL,
        'prefix' => 'websockets',
        'where' => 
        array (
        ),
        'as' => 'generated::pRhWRVd1WZvPpSzJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::BX4EaVeKpaeIvduG' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'websockets/event',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Middleware\\Authorize',
        ),
        'uses' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\SendMessage@__invoke',
        'controller' => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Controllers\\SendMessage',
        'namespace' => NULL,
        'prefix' => 'websockets',
        'where' => 
        array (
        ),
        'as' => 'generated::BX4EaVeKpaeIvduG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Df2SPcLhvp3eJqPK' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'websockets/statistics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'BeyondCode\\LaravelWebSockets\\Statistics\\Http\\Middleware\\Authorize',
        ),
        'uses' => 'BeyondCode\\LaravelWebSockets\\Statistics\\Http\\Controllers\\WebSocketStatisticsEntriesController@store',
        'controller' => 'BeyondCode\\LaravelWebSockets\\Statistics\\Http\\Controllers\\WebSocketStatisticsEntriesController@store',
        'namespace' => NULL,
        'prefix' => 'websockets',
        'where' => 
        array (
        ),
        'as' => 'generated::Df2SPcLhvp3eJqPK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.stats.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/stats',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\DashboardStatsController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\DashboardStatsController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.stats.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.workload.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/workload',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\WorkloadController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\WorkloadController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.workload.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.masters.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/masters',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\MasterSupervisorController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\MasterSupervisorController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.masters.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.monitoring.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/monitoring',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.monitoring.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.monitoring.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'horizon/api/monitoring',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@store',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@store',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.monitoring.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.monitoring-tag.paginate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/monitoring/{tag}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@paginate',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@paginate',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.monitoring-tag.paginate',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.monitoring-tag.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'horizon/api/monitoring/{tag}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@destroy',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\MonitoringController@destroy',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.monitoring-tag.destroy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.jobs-metrics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/metrics/jobs',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\JobMetricsController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\JobMetricsController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.jobs-metrics.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.jobs-metrics.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/metrics/jobs/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\JobMetricsController@show',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\JobMetricsController@show',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.jobs-metrics.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.queues-metrics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/metrics/queues',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\QueueMetricsController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\QueueMetricsController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.queues-metrics.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.queues-metrics.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/metrics/queues/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\QueueMetricsController@show',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\QueueMetricsController@show',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.queues-metrics.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.jobs-batches.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/batches',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\BatchesController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\BatchesController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.jobs-batches.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.jobs-batches.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/batches/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\BatchesController@show',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\BatchesController@show',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.jobs-batches.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.jobs-batches.retry' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'horizon/api/batches/retry/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\BatchesController@retry',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\BatchesController@retry',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.jobs-batches.retry',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.pending-jobs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/jobs/pending',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\PendingJobsController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\PendingJobsController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.pending-jobs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.completed-jobs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/jobs/completed',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\CompletedJobsController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\CompletedJobsController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.completed-jobs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.failed-jobs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/jobs/failed',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\FailedJobsController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\FailedJobsController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.failed-jobs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.failed-jobs.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/jobs/failed/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\FailedJobsController@show',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\FailedJobsController@show',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.failed-jobs.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.retry-jobs.show' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'horizon/api/jobs/retry/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\RetryController@store',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\RetryController@store',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.retry-jobs.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.jobs.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/api/jobs/{id}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\JobsController@show',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\JobsController@show',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon/api',
        'where' => 
        array (
        ),
        'as' => 'horizon.jobs.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'horizon.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'horizon/{view?}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Laravel\\Horizon\\Http\\Controllers\\HomeController@index',
        'controller' => 'Laravel\\Horizon\\Http\\Controllers\\HomeController@index',
        'namespace' => 'Laravel\\Horizon\\Http\\Controllers',
        'prefix' => 'horizon',
        'where' => 
        array (
        ),
        'as' => 'horizon.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'view' => '(.*)',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graphql-playground' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graphql-playground',
      'action' => 
      array (
        'as' => 'graphql-playground',
        'uses' => 'MLL\\GraphQLPlayground\\GraphQLPlaygroundController@__invoke',
        'controller' => 'MLL\\GraphQLPlayground\\GraphQLPlaygroundController',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graphql' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'POST',
        2 => 'HEAD',
      ),
      'uri' => 'graphql',
      'action' => 
      array (
        'as' => 'graphql',
        'uses' => 'Nuwave\\Lighthouse\\Support\\Http\\Controllers\\GraphQLController@__invoke',
        'middleware' => 
        array (
          0 => 'Nuwave\\Lighthouse\\Support\\Http\\Middleware\\AcceptJson',
          1 => 'Nuwave\\Lighthouse\\Support\\Http\\Middleware\\AttemptAuthentication',
          2 => 'App\\Http\\Middleware\\ConfigureAppLanguage',
        ),
        'controller' => 'Nuwave\\Lighthouse\\Support\\Http\\Controllers\\GraphQLController',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Wb4S6kotkGbdriMU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/mail',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MailController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MailController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::Wb4S6kotkGbdriMU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::l0uBjZk0EO4Ds1Ds' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/mail/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MailController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MailController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::l0uBjZk0EO4Ds1Ds',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DYNuOUFK6rlUPKDU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/mail/{telescopeEntryId}/preview',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MailHtmlController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MailHtmlController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::DYNuOUFK6rlUPKDU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::I4jXmG62biLcDgd3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/mail/{telescopeEntryId}/download',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MailEmlController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MailEmlController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::I4jXmG62biLcDgd3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ZELAfFBHoeRQrwB3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/exceptions',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ExceptionController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ExceptionController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::ZELAfFBHoeRQrwB3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9Nj3w7NwBeRvoTkX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/exceptions/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ExceptionController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ExceptionController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::9Nj3w7NwBeRvoTkX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::S9wg1HmU28nuYsfw' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'telescope/telescope-api/exceptions/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ExceptionController@update',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ExceptionController@update',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::S9wg1HmU28nuYsfw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::l6qUEwbpsUsGvgTB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/dumps',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\DumpController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\DumpController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::l6qUEwbpsUsGvgTB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1CaEmQtHFyQCDh5B' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/logs',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\LogController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\LogController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::1CaEmQtHFyQCDh5B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ARvplLWhINJ32fDS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/logs/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\LogController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\LogController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::ARvplLWhINJ32fDS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::P9tOgd6iYw0oVjCi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/notifications',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\NotificationsController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\NotificationsController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::P9tOgd6iYw0oVjCi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gRzLRSvfcnqwFxsF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/notifications/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\NotificationsController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\NotificationsController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::gRzLRSvfcnqwFxsF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NaZjx6jpNy8blDIu' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/jobs',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\QueueController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\QueueController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::NaZjx6jpNy8blDIu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6gnYtPrIUD92U3Hu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/jobs/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\QueueController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\QueueController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::6gnYtPrIUD92U3Hu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MDHzn2IBbSBmAogm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/batches',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\QueueBatchesController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\QueueBatchesController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::MDHzn2IBbSBmAogm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HJqjFuFn7CCJc3zg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/batches/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\QueueBatchesController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\QueueBatchesController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::HJqjFuFn7CCJc3zg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ttvEQ4eEzFhPCdTB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/events',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\EventsController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\EventsController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::ttvEQ4eEzFhPCdTB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::G5aN8qGECkNv3RoO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/events/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\EventsController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\EventsController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::G5aN8qGECkNv3RoO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LFGQnmE2ymfexa1M' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/gates',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\GatesController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\GatesController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::LFGQnmE2ymfexa1M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CIuF8B1PcvcKFawx' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/gates/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\GatesController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\GatesController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::CIuF8B1PcvcKFawx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fg75nwXey7xqKSth' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/cache',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\CacheController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\CacheController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::fg75nwXey7xqKSth',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nZpfaBkxiNd6uGhW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/cache/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\CacheController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\CacheController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::nZpfaBkxiNd6uGhW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::0iO0CzBh5795z9yj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/queries',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\QueriesController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\QueriesController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::0iO0CzBh5795z9yj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XeOA9QcMYZvTZsWW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/queries/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\QueriesController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\QueriesController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::XeOA9QcMYZvTZsWW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GdPQGNP3cXxmF5VL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/models',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ModelsController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ModelsController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::GdPQGNP3cXxmF5VL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MozA1OOoFltls87b' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/models/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ModelsController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ModelsController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::MozA1OOoFltls87b',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5P5XVQ6EXjVEyWAk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/requests',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\RequestsController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\RequestsController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::5P5XVQ6EXjVEyWAk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yeXe9bxTrG0Vm7mC' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/requests/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\RequestsController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\RequestsController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::yeXe9bxTrG0Vm7mC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UYhHOd9j4hXCaT74' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/views',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ViewsController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ViewsController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::UYhHOd9j4hXCaT74',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ZB1biQCfBIOMQvHo' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/views/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ViewsController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ViewsController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::ZB1biQCfBIOMQvHo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gl0SyYyL0SmgcIH2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/commands',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\CommandsController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\CommandsController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::gl0SyYyL0SmgcIH2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::thHlRIGdTNxsOqJK' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/commands/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\CommandsController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\CommandsController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::thHlRIGdTNxsOqJK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::D96FhI341rmPlKR6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/schedule',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ScheduleController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ScheduleController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::D96FhI341rmPlKR6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aJh7WTC8Lt2ij85g' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/schedule/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ScheduleController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ScheduleController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::aJh7WTC8Lt2ij85g',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PsoP3mulh6YZBZHC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/redis',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\RedisController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\RedisController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::PsoP3mulh6YZBZHC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::T5IKpS2LPeF832HC' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/redis/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\RedisController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\RedisController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::T5IKpS2LPeF832HC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YxkORXeFF1XAtrxf' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/client-requests',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ClientRequestController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ClientRequestController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::YxkORXeFF1XAtrxf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CHYpSkhbPzg11VIM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/client-requests/{telescopeEntryId}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\ClientRequestController@show',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\ClientRequestController@show',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::CHYpSkhbPzg11VIM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ayVXJVphugrb6mue' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/telescope-api/monitored-tags',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MonitoredTagController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MonitoredTagController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::ayVXJVphugrb6mue',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aMQzcn76hvocD9MU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/monitored-tags',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MonitoredTagController@store',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MonitoredTagController@store',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::aMQzcn76hvocD9MU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yobXYqlhc3LNpy5M' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/monitored-tags/delete',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\MonitoredTagController@destroy',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\MonitoredTagController@destroy',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::yobXYqlhc3LNpy5M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QdopWbJLk4e5fXCR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'telescope/telescope-api/toggle-recording',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\RecordingController@toggle',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\RecordingController@toggle',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::QdopWbJLk4e5fXCR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cXnq2HtRt2Tbr5ze' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'telescope/telescope-api/entries',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\EntriesController@destroy',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\EntriesController@destroy',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'generated::cXnq2HtRt2Tbr5ze',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'telescope' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'telescope/{view?}',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 'telescope',
        'uses' => 'Laravel\\Telescope\\Http\\Controllers\\HomeController@index',
        'controller' => 'Laravel\\Telescope\\Http\\Controllers\\HomeController@index',
        'namespace' => 'Laravel\\Telescope\\Http\\Controllers',
        'prefix' => 'telescope',
        'where' => 
        array (
        ),
        'as' => 'telescope',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'view' => '(.*)',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::e1l9MKGVKn8Jjw3B' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'POST',
        2 => 'HEAD',
      ),
      'uri' => 'broadcasting/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'auth:user,driver,partner,admin',
        ),
        'uses' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'controller' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'excluded_middleware' => 
        array (
          0 => 'Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken',
        ),
        'as' => 'generated::e1l9MKGVKn8Jjw3B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::trhkrb6qICKrkWlg' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\AdminController@login',
        'controller' => 'App\\Http\\Controllers\\Mutations\\AdminController@login',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::trhkrb6qICKrkWlg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6Oz4BlAjxcIUE8wj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/driver/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\DriverController@login',
        'controller' => 'App\\Http\\Controllers\\Mutations\\DriverController@login',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::6Oz4BlAjxcIUE8wj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gB16couXrZ4803BW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/user/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\UserController@create',
        'controller' => 'App\\Http\\Controllers\\Mutations\\UserController@create',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::gB16couXrZ4803BW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qnJvLIN5qmAAndXX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/user/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\UserController@login',
        'controller' => 'App\\Http\\Controllers\\Mutations\\UserController@login',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::qnJvLIN5qmAAndXX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dpLCUSqrp7opOFiM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/user/social/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\UserController@socialLogin',
        'controller' => 'App\\Http\\Controllers\\Mutations\\UserController@socialLogin',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::dpLCUSqrp7opOFiM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OMTSu4uhheZSocNL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/terminal/transaction',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripTerminalTransactionController@create',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripTerminalTransactionController@create',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::OMTSu4uhheZSocNL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eH5Xse68K01W7fkA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/user/avatar/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:user',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\UserController@handleAvatar',
        'controller' => 'App\\Http\\Controllers\\Mutations\\UserController@handleAvatar',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::eH5Xse68K01W7fkA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fvJuOHFFrNl5ibLc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/driver/avatar/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\DriverController@handleAvatar',
        'controller' => 'App\\Http\\Controllers\\Mutations\\DriverController@handleAvatar',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::fvJuOHFFrNl5ibLc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ro9oI4boGtC9NJVo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/driver/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\DriverController@update',
        'controller' => 'App\\Http\\Controllers\\Mutations\\DriverController@update',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::Ro9oI4boGtC9NJVo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::z5LKlBd7xHCTqYBb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/driver/update/password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\DriverController@updatePassword',
        'controller' => 'App\\Http\\Controllers\\Mutations\\DriverController@updatePassword',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::z5LKlBd7xHCTqYBb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::53AaodVLB97TsMCB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/ready',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@ready',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@ready',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::53AaodVLB97TsMCB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::apOdVXxSRkc5jXjC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/start',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@start',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@start',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::apOdVXxSRkc5jXjC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::K6WuBJH9x129PQqJ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/end',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@end',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@end',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::K6WuBJH9x129PQqJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YVjHxfOLfejHqpEw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/at/station',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@atStation',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@atStation',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::YVjHxfOLfejHqpEw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PLtRyO3S4uaI1xfV' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/pick/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@pickUsers',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@pickUsers',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::PLtRyO3S4uaI1xfV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::o0aaDtcqvZM3crUN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/drop/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@dropUsers',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@dropUsers',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::o0aaDtcqvZM3crUN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JFl6YiO2c6GDEKm5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/update/driver/location',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@updateDriverLocation',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@updateDriverLocation',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::JFl6YiO2c6GDEKm5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IbIWlVLccxhOmjW7' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/event/change/attendance/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@changeAttendanceStatus',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripEventController@changeAttendanceStatus',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::IbIWlVLccxhOmjW7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GXH4n24wrbseqTBN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/ready',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@ready',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@ready',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::GXH4n24wrbseqTBN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dJy5SwGri71lX0nh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/start',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@start',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@start',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::dJy5SwGri71lX0nh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HqIMfWcvhuavqqb3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/end',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@end',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@end',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::HqIMfWcvhuavqqb3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CUkaiT0NNVXQcTMM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/at/station',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@atStation',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@atStation',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::CUkaiT0NNVXQcTMM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::r51rWMALaOIbiYYa' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/pick/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@pickUser',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@pickUser',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::r51rWMALaOIbiYYa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::e4gZOA2uKda73YzD' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/drop/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@dropUser',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@dropUser',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::e4gZOA2uKda73YzD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GqP6nrdUwPsBNMAs' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/event/update/driver/location',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@updateDriverLocation',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripEventController@updateDriverLocation',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::GqP6nrdUwPsBNMAs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sLMor9lyD7Tj9Z8i' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/business/trip/attendance/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\BusinessTripAttendanceController@create',
        'controller' => 'App\\Http\\Controllers\\Mutations\\BusinessTripAttendanceController@create',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::sLMor9lyD7Tj9Z8i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XwYnzialDhPETmvN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/send/message',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\CommunicationController@sendBusinessTripChatMessage',
        'controller' => 'App\\Http\\Controllers\\Mutations\\CommunicationController@sendBusinessTripChatMessage',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::XwYnzialDhPETmvN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::td9fA0K9u2mdGQWW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/booking/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripBookingController@update',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripBookingController@update',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::td9fA0K9u2mdGQWW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hZVPaiJ0p1MW9ARU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/pos/transaction',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripPosTransactionController@create',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripPosTransactionController@create',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::hZVPaiJ0p1MW9ARU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UxbnU6ntqHywT8eW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rest/seats/trip/pos/bulk-transaction',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Mutations\\SeatsTripPosTransactionController@bulkCreate',
        'controller' => 'App\\Http\\Controllers\\Mutations\\SeatsTripPosTransactionController@bulkCreate',
        'namespace' => 'App\\Http\\Controllers\\Mutations',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::UxbnU6ntqHywT8eW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rfEElhRzRFSmyOg2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\DriverController@auth',
        'controller' => 'App\\Http\\Controllers\\Queries\\DriverController@auth',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::rfEElhRzRFSmyOg2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TfFlN1X3U6KlAMac' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\DriverController@show',
        'controller' => 'App\\Http\\Controllers\\Queries\\DriverController@show',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::TfFlN1X3U6KlAMac',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::t9pjl4DupBGkIqZt' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@show',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@show',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::t9pjl4DupBGkIqZt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rHQpizHJgbKZ3s6z' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{driver_id}/seats/trips',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@driverSeatsTrips',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@driverSeatsTrips',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::rHQpizHJgbKZ3s6z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oDaIT21b1ENXdJBF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/{trip_id}/stations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@businessTripStations',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@businessTripStations',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::oDaIT21b1ENXdJBF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KQ3c9fvEynLff9Qw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/seats/trip/line/{line_id}/stations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@seatsTripLineStations',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@seatsTripLineStations',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::KQ3c9fvEynLff9Qw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PeIHjhayIiWLIvOg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{driver_id}/business/trips/schedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@driverBusinessTripsSchedule',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@driverBusinessTripsSchedule',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::PeIHjhayIiWLIvOg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MrWly0C5pCuAN6V5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{driver_id}/live/business/trips',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@driverLiveBusinessTrips',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripController@driverLiveBusinessTrips',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::MrWly0C5pCuAN6V5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ghj62yHIxQSCVknD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{driver_id}/seats/trips/schedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@driverSeatsTripsSchedule',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@driverSeatsTripsSchedule',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::Ghj62yHIxQSCVknD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nI7jCoM8DaHRjq4l' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{driver_id}/live/seats/trips',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@driverLiveSeatsTrips',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@driverLiveSeatsTrips',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::nI7jCoM8DaHRjq4l',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QYSLxaq18SlDzOCA' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/seats/trip/{trip_id}/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripUserController@users',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripUserController@users',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::QYSLxaq18SlDzOCA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ON0faokFdMnswmm3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/{trip_id}/attendance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripAttendanceController@businessTripAttendance',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripAttendanceController@businessTripAttendance',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::ON0faokFdMnswmm3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UxK5cXIh7UtbwoOf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/seats/trip/{trip_id}/app/transactions/detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@seatsTripAppTransactionsDetail',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripController@seatsTripAppTransactionsDetail',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::UxK5cXIh7UtbwoOf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::l0KmkaMk0WIQeGd9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/user/{user_id}/business/trip/chat/messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\CommunicationController@businessTripChatMessages',
        'controller' => 'App\\Http\\Controllers\\Queries\\CommunicationController@businessTripChatMessages',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::l0KmkaMk0WIQeGd9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::y0xPmx5bmv43jv8g' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/private/chat/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\CommunicationController@businessTripPrivateChatUsers',
        'controller' => 'App\\Http\\Controllers\\Queries\\CommunicationController@businessTripPrivateChatUsers',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::y0xPmx5bmv43jv8g',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::q4PH399CxGU34ZXt' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/{trip_id}/subscribers',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripSubscriptionController@businessTripSubscribers',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripSubscriptionController@businessTripSubscribers',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::q4PH399CxGU34ZXt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ISvzK1oOggTypnkm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/users/status/{trip_id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripSubscriptionController@businessTripUsersStatus',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripSubscriptionController@businessTripUsersStatus',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::ISvzK1oOggTypnkm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cAQeP24QolIzaRA6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/business/trip/{trip_id}/user/{user_id}/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\BusinessTripSubscriptionController@businessTripUserStatus',
        'controller' => 'App\\Http\\Controllers\\Queries\\BusinessTripSubscriptionController@businessTripUserStatus',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::cAQeP24QolIzaRA6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::N4HAKJcXXlDO28QR' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/partner/{partner_id}/payment-categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\PaymentCategoryController@partnerPaymentCategories',
        'controller' => 'App\\Http\\Controllers\\Queries\\PaymentCategoryController@partnerPaymentCategories',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::N4HAKJcXXlDO28QR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wfKYk3T6iaIABfd0' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/user/{user_id}/device/id',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\UserController@userDeviceId',
        'controller' => 'App\\Http\\Controllers\\Queries\\UserController@userDeviceId',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::wfKYk3T6iaIABfd0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IEGjHdG4lQEMraPv' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/driver/{driver_id}/device/id',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\DriverController@driverDeviceId',
        'controller' => 'App\\Http\\Controllers\\Queries\\DriverController@driverDeviceId',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::IEGjHdG4lQEMraPv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9tHa5s67Il9nxmWf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/drivers/device/id',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\DriverController@driversDeviceId',
        'controller' => 'App\\Http\\Controllers\\Queries\\DriverController@driversDeviceId',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::9tHa5s67Il9nxmWf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lpunMfAVqFCM4YYV' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/seats/trip/pos/vehicle/{vehicle_id}/max-serial',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:driver',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripPosTransactionController@vehicleMaxSerial',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripPosTransactionController@vehicleMaxSerial',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::lpunMfAVqFCM4YYV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Vfy4JX6AUUS3s8wj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/seats/trip/terminal/transactions/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:admin,partner',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripTerminalTransactionController@export',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripTerminalTransactionController@export',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::Vfy4JX6AUUS3s8wj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LVaqytBAKq7WA5zb' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rest/seats/trip/pos/transactions/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:admin,partner',
        ),
        'uses' => 'App\\Http\\Controllers\\Queries\\SeatsTripPosTransactionController@export',
        'controller' => 'App\\Http\\Controllers\\Queries\\SeatsTripPosTransactionController@export',
        'namespace' => 'App\\Http\\Controllers\\Queries',
        'prefix' => 'rest',
        'where' => 
        array (
        ),
        'as' => 'generated::LVaqytBAKq7WA5zb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HrximAro8VChtr0M' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\WelcomeController@index',
        'controller' => 'App\\Http\\Controllers\\WelcomeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::HrximAro8VChtr0M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.reset' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '{type}/password/reset/{token}?email={email}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\ResetPasswordController@showResetForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\ResetPasswordController@showResetForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.reset',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
