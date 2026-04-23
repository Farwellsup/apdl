 @if (Auth::user())
        @if (Request::segment(1) === 'courses' && Auth::user()->initial_profile == 0)
            <script>
                $(document).ready(function() {
                    $('#changePass').modal('show');
                });
            </script>
    @endif
@endif