<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>

        <link rel="icon" type="image/gif" href="{{ url('favicon.ico') }}"/>
        <link rel="apple-touch-icon" href="{{ url('app_icon.png') }}"/>

        <title>JUSTICE</title>

        <script>

            let localhost = {{ config('const.LOCALHOST') }};
            let user = '{{ config("const.USER") }}';

            let load_date = '{{ date("Y-n-j") }}';

            window.addEventListener('focus', () => {

                const d = new Date();
                let current_date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();

                if (current_date !== load_date) location.reload();

            });

        </script>

    </head>

    <body>

        <div id="app">

            <nav-bar></nav-bar>

            <main>

                <router-view v-slot="{ Component }">

                    <keep-alive>

                        <suspense>

                            <component :is="Component"/>

                        </suspense>

                    </keep-alive>

                </router-view>

            </main>

            <notify></notify>

            <pop-up></pop-up>

        </div>

        @vite('resources/js/app.js')

    </body>

</html>
