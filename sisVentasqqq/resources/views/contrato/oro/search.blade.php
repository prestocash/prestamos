{!! Form::open(array('url'=>'/contrato/oro','method'=>'GET','autocomplete'=>'off','role'=>'search')) !!}
<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
	<div class="form-group">
		<div class="input-group">
			<input type="text" class="form-control" name="searchText" placeholder="Buscar por Codigo..." value="{{$searchText}}">
					<span class="input-group-btn">
				<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Buscar</button>
			
			</span>
		</div>
	</div>
</div>

{{Form::close()}}