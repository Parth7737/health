<div class="btn--container justify-content-center">
    <a href="{{ route('admin.procedure.edit', [$procedure->id]) }}" class="btn action-btn btn--primary btn-xs btn-outline-primary mt-1" title="Edit Procedure">
        <i class="fa fa-edit"></i>
    </a>
    <a class="btn action-btn btn--danger btn-xs btn-outline-danger mt-1" href="javascript:"
        onclick="form_alert('procedure-{{$procedure->id}}','Want to delete this procedure ?')" title="Delete Procedure">
        <i class="fa fa-trash"></i>
    </a>
    <form action="{{ route('admin.procedure.destroy', [$procedure->id]) }}" method="post" id="procedure-{{$procedure->id}}">
        @csrf @method('delete')
    </form>
</div>
