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
            <ul>
                <li>
                    <a @if( url()->current() == route('products.bulk') ) class="is-active" @endif href="{{ route('products.bulk') }}">
                        Bulk upload product
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a @if( url()->current() == route('customers') ) class="is-active" @endif href="{{ route('customers') }}">
                My customers
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('lots') ) class="is-active" @endif href="{{ route('lots') }}">
                My lots
            </a>
            <ul>
                <li>
                    <a @if( url()->current() == route('payment') ) class="is-active" @endif href="{{ route('payment') }}">
                        Purchase history
                    </a>
                </li>
                <li>
                    <a href="{{ route('payment') . '?new=true' }}">
                        Purchase new lots
                    </a>
                </li>
            </ul>
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
            <a @if( url()->current() == route('returns') ) class="is-active" @endif href="{{ route('returns') }}">
                Return Order
            </a>
        </li>
    </ul>
</aside>