<aside class="menu">
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('profile') ) class="is-active" @endif href="{{ route('profile') }}">
                My profile
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('products') ) class="is-active" @endif href="{{ route('products') }}">
                My products
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('lots') ) class="is-active" @endif href="{{ route('lots') }}">
                My lots
            </a>
        </li>
    </ul>
    <p class="menu-label">
        My orders
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
        <li>
            <a @if( url()->current() == route('payment') ) class="is-active" @endif href="{{ route('payment') }}">
                Purchases
            </a>
            <ul>
                <li>
                    <a href="{{ route('payment') . '?new=true' }}">
                        Purchase new lots
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>