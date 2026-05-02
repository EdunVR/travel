@hasPermission('service.mesin.create')
<div>PERMISSION CHECK PASSED - Button should show</div>
@else
<div>PERMISSION CHECK FAILED - Button will be hidden</div>
@endhasPermission