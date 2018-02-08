<aside class="menu">
    <p class="menu-label">
    	Lots management
  	</p>
  	<ul class="menu-list">
    	<li>
            <a @if( url()->current() == route('lots.categories') ) class="is-active" @endif
            href="{{ route('lots.categories') }}">
                Categories
            </a>
        </li>
  	</ul>
</aside>