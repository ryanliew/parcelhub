<aside class="menu">  
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('dashboard') ) class="is-active" @endif href="{{ route('dashboard') }}">
                Dashboard
            </a>
        </li>

        <li>
            <a @if( url()->current() == route('profile') ) class="is-active" @endif href="{{ route('profile') }}">
                My profile
            </a>
        </li>
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
        <li>
            <a @if( url()->current() == route('products') ) class="is-active" @endif href="{{ route('products') }}">
                Products
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('users') ) class="is-active" @endif href="{{ route('users') }}">
                Users
            </a>
        </li>
        @role('superadmin')
        <li>
            <a @if( url()->current() == route('branches') ) class="is-active" @endif href="{{ route('branches') }}">
                Branches
            </a>
        </li>
        @endrole
    </ul>

    <p class="menu-label">
        Orders
    </p>
    <ul class="menu-list">
        <li id='inbound-menu' class="notifiable @if($hasInbounds && url()->current() !== route('inbounds')) has-notification @endif">
            
            <a @if( url()->current() == route('inbounds') ) class="is-active" @endif href="{{ route('inbounds') }}">
                Inbounds
            </a>
        </li>
        <li id='outbound-menu' class="notifiable @if($hasOutbounds && url()->current() !== route('outbounds')) has-notification @endif">
            <a @if( url()->current() == route('outbounds') ) class="is-active" @endif href="{{ route('outbounds') }}">
                Outbounds
            </a>
            <ul>
                <li>
                    <a @if( url()->current() == route('outbounds.bulk') ) class="is-active" @endif href="{{ route('outbounds.bulk') }}">
                        Scan tracking numbers
                    </a>
                </li>
            </ul>
        </li>
        <li id='return-menu' class="notifiable @if($hasReturns && url()->current() !== route('returns')) has-notification @endif">
            <a @if( url()->current() == route('returns') ) class="is-active" @endif href="{{ route('returns') }}">
                Return Order
            </a>
        </li>
        <li id='recall-menu' class="notifiable @if($hasRecalls && url()->current() !== route('recalls')) has-notification @endif">
            <a @if( url()->current() == route('recalls') ) class="is-active" @endif href="{{ route('recalls') }}">
                Recall Order
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('payment') ) class="is-active" @endif href="{{ route('payment') }}">
                Purchases
            </a>
        </li>
    </ul>

    <p class="menu-label">
        Misc
    </p>
    <ul class="menu-list">
        <li>
            <a @if( url()->current() == route('setting.index') ) class="is-active" @endif href="{{ route('setting.index') }}">
                System settings
            </a>
        </li>
        <li>
            <a @if( url()->current() == route('reports') ) class="is-active" @endif href="{{ route('reports') }}">
                Reports
            </a>
        </li>
    </ul>
</aside>