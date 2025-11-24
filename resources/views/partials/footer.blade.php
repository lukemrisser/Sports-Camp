<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-content">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Produced by Falcon Teams</p>
        </div>

        <div class="footer-links">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('help') }}">Help</a></li>
                <li><a href="{{ route('home') }}">Privacy Policy</a></li>
                <li><a href="{{ route('home') }}">Terms of Service</a></li>
            </ul>
        </div>


    </div>

</footer>
