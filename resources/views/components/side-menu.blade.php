@role('admin')
	@include('components.side-menu-admin')
@endrole

@role('user')
	@include('components.side-menu-user')
@endrole