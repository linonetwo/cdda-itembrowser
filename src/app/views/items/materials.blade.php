@section('title')
材料 - Cataclysm: Dark Days Ahead
@endsection
<h1>材料</h1>

<div class="row">
  <div class="col-md-3">
<ul class="nav nav-pills nav-stacked">
@foreach($materials as $material)
<li class="@if($material->ident==$id) active @endif"><a href="{{ route(Route::currentRouteName(), $material->ident) }}">{{{$material->name}}}</a></li>
@endforeach
</ul>
  </div>

  <div class="col-md-9">
@if (!$id)
Please select an entry from the menu on the left.
@else
<table class="table table-bordered table-hover tablesorter">
  <thead>
  <tr>
    <th></th>
    <th>名称</th>
    <th>体积(L)</th>
    <th>质量(KG)</th>
  </tr>
</thead>
@foreach($items as $item)
<tr>
  <td>{{ $item->symbol }}</td>
  <td><a href="{{route('item.view', $item->id)}}">{{ $item->name }} {{ $item->modLabel }}</a></td>
  <td>{{{ $item->volume }}}</td>
  <td>{{{ $item->weightMetric }}}</td>
</tr>
</tr>
@endforeach
</table>
<script>
$(function() {
    $(".tablesorter").tablesorter({
      sortList: [[1,0]]
      });
});
</script>
@endif
</div>
</div>
