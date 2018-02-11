<aside class="menu">
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('couriers') ) class="is-active" @endif href="{{ route('couriers') }}">
                Couriers
            </a>
        </li>
    </ul>
    
    <p class="menu-label">
    	Lots management
  	</p>
  	<ul class="menu-list">
        <li>
            <a @if( url()->current() == route('lots.categories') ) class="is-active" @endif href="{{ route('lots.categories') }}">
                Categories
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('lots') ) class="is-active" @endif href="{{ route('lots') }}">
                Lots
            </a>
        </li>
  	</ul>
</aside>