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
            @if(!auth()->user()->hasRole('subuser'))
            <ul>
                <li>
                    <a @if( url()->current() == route('products.excel') ) class="is-active" @endif href="{{ route('products.excel') }}">
                        Bulk upload product
                    </a>
                </li>
            </ul>
            @endif
        </li>
        @if(!auth()->user()->hasRole('subuser'))
        <li>
            <a @if( url()->current() == route('customers') ) class="is-active" @endif href="{{ route('customers') }}">
                My customers
            </a>
        </li>
        @endif
        <li>
            <a @if( url()->current() == route('lots') ) class="is-active" @endif href="{{ route('lots') }}">
                My lots
            </a>
            @if(!auth()->user()->hasRole('subuser'))
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
            @endif
        </li>
        @if(!auth()->user()->hasRole('subuser'))
        <li>
            <a @if( url()->current() == route('subusers') ) class="is-active" @endif href="{{ route('subusers') }}">
                My subusers
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('reports') ) class="is-active" @endif href="{{ route('reports') }}">
                Reports
            </a>
        </li>
        @endif
    </ul>
    <p class="menu-label">
        My orders
    </p>
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('inbounds') ) class="is-active" @endif href="{{ route('inbounds') }}">
                Inbounds
            </a>
            @if(!auth()->user()->hasRole('subuser'))
            <ul>
                <li>
                    <a @if( url()->current() == route('inbounds.excel') ) class="is-active" @endif href="{{ route('inbounds.excel') }}">
                        Bulk upload inbounds
                    </a>
                </li>
            </ul>
            @endif
        </li>
        <li>
            <a @if( url()->current() == route('outbounds') ) class="is-active" @endif href="{{ route('outbounds') }}">
                Outbounds
            </a>
            @if(!auth()->user()->hasRole('subuser'))
            <ul>
                <li>
                    <a @if( url()->current() == route('outbounds.excel') ) class="is-active" @endif href="{{ route('outbounds.excel') }}">
                        Bulk upload outbounds
                    </a>
                </li>
            </ul>
            @endif
        </li>
        <li>
            <a @if( url()->current() == route('returns') ) class="is-active" @endif href="{{ route('returns') }}">
                Return Order
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('recalls') ) class="is-active" @endif href="{{ route('recalls') }}">
                Recall Order
            </a>
        </li>
    </ul>
</aside>