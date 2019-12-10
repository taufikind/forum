<div class="card">
    <div class="card-header" style="background: #2ab27b; color: #fff; padding: 8px 1.25rem;">Popular</div>

    <div class="list-group">
    @foreach($populars as $popular)
        <a href="{{route('forumslug',$popular->slug)}}" class="list-group-item" id="index_hover">{{$popular->title}}      
    @endforeach
        </a> 
    </div>
</div>