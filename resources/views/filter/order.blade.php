
<form  method="get" class="row">
    <div class="col-5">
        <label class="form-label" for="inputName2">From</label>
        <input class="form-control" name="startDate" value="{{ request()->get('startDate', date('Y-m-d'))  }}" type="date" placeholder="From">
    </div>
    <div class="col-5">
        <label class="form-label" for="inputName2">To</label>
        <input class="form-control" name="stopDate"  value="{{  request()->get('stopDate', date('Y-m-d')) }}" type="date" placeholder="To">
    </div>
    <div class="col-2 mt-1 d-flex">
        <button type="submit" class="btn btn-sm btn-outline-dark mt-4">Filter</button>
        &nbsp; &nbsp;
        <a href="{{ $filterResetLink }}" class="btn btn-sm btn-outline-danger mt-4">Reset</a>
    </div>
</form>