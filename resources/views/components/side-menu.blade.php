<aside class="menu">
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('products') ) class="is-active" @endif href="{{ route('products') }}">
                Products
            </a>
        </li>
    </ul>
    <p class="menu-label">
        Orders
    </p>
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('inbounds') ) class="is-active" @endif href="{{ route('inbounds') }}">
                Inbounds
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('outbounds') ) class="is-active" @endif href="{{ route('outbounds') }}">
                Outbounds
            </a>
        </li>
    </ul>
    <p class="menu-label">
    	Admin
  	</p>
  	<ul class="menu-list">
        <li>
            <a @if( url()->current() == route('lots.categories') ) class="is-active" @endif href="{{ route('lots.categories') }}">
                Lot categories
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('lots') ) class="is-active" @endif href="{{ route('lots') }}">
                Lots
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('couriers') ) class="is-active" @endif href="{{ route('couriers') }}">
                Couriers
            </a>
        </li>
  	</ul>
</aside>