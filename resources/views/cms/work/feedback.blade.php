@if(session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
