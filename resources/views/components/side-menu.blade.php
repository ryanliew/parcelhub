@role(['admin', 'superadmin'])
	@include('components.side-menu-admin')
@endrole

@role('user')
	@include('components.side-menu-user')
@endrole

@role('subuser')
	@include('components.side-menu-user')
@endrole