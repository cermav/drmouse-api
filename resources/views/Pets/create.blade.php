@extends('Pets')
@section('main')
<div class="row">
 <div class="col-sm-8 offset-sm-2">
    <h1 class="display-3">Add a pet</h1>
  <div>
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
      </div><br />
    @endif
      <form method="post" action="{{ route('Pets.store') }}">
          @csrf
          <div class="form-group">    
              <label for="owners_id">First Name:</label>
              <input type="text" class="form-control" name="owners_id"/>
          </div>
          <div class="form-group">
              <label for="pet_name">Last Name:</label>
              <input type="text" class="form-control" name="pet_name"/>
          </div>
          <div class="form-group">
              <label for="birth_date">Email:</label>
              <input type="text" class="form-control" name="birth_date"/>
          </div>
          <div class="form-group">
              <label for="kind">City:</label>
              <input type="text" class="form-control" name="kind"/>
          </div>
          <div class="form-group">
              <label for="breed">Country:</label>
              <input type="text" class="form-control" name="breed"/>
          </div>
          <div class="form-group">
              <label for="gender">Job Title:</label>
              <input type="text" class="form-control" name="gender"/>
          </div>  
          <div class="form-group">
              <label for="chip_number">Job Title:</label>
              <input type="text" class="form-control" name="chip_number"/>
          </div>   
          <div class="form-group">
              <label for="bg">Job Title:</label>
              <input type="text" class="form-control" name="bg"/>
          </div>      
          <div class="form-group">
              <label for="profile_completedness">Job Title:</label>
              <input type="text" class="form-control" name="profile_completedness"/>
          </div>                                                                    
          <button type="submit" class="btn btn-primary-outline">Add contact</button>
      </form>
  </div>
</div>
</div>
@endsection