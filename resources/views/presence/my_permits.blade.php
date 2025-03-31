 @if (session('success'))
 <div class="p-0">
   <div class="alert alert-success text-white">
     {{ session('success') }}
   </div>
 </div>
 @endif

 <div class="card">
   <nav>
     <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
       <a data-mdb-ripple-init class="nav-link active" id="nav-harian-tab" data-bs-toggle="tab" href="#nav-harian" role="tab" aria-controls="nav-harian" aria-selected="true">Harian</a>
       <a data-mdb-ripple-init class="nav-link" id="nav-berjangka-tab" data-bs-toggle="tab" href="#nav-berjangka" role="tab" aria-controls="nav-berjangka" aria-selected="false">Berjangka</a>
     </div>

     <div class="tab-content p-0" id="nav-tabContent">
       <div class="tab-pane fade show active" id="nav-harian" role="tabpanel" aria-labelledby="nav-harian-tab">
         <div class="card-header align-items-center">
           <a data-mdb-ripple-init class="btn btn-primary btn-block btn-rounded mb-2 mt-2" href="{{ (auth()->user()->hasRole('superadmin')) ? route('create presence permit') : route('presence permit submission') }}" onclick="return false">
             <i class="fas fa-plus" aria-hidden="true"></i>
             <b>Buat izin</b>
           </a>
         </div>

         <div class="datatable table-responsive p-2">
           <table id="table-izin" class="table align-items-center mb-0">
             <thead style="background-color:#f6f9fc;">
               <tr>
                 <th class="text-uppercase text-secondary font-weight-bolder">Nama</th>
                 <th class="text-uppercase text-secondary font-weight-bolder ps-2" style="width:20%;">Alasan</th>
                 <th class="text-uppercase text-secondary font-weight-bolder ps-2">Status</th>
                 <th class="text-uppercase text-secondary text-center font-weight-bolder ps-2">Action</th>
               </tr>
             </thead>
             <tbody>
               @foreach($myPermits as $myPermit)
               <tr class="text-sm">
                 <td>
                   <h6 class="mb-0 text-sm">{{ ($myPermit->santri) ? $myPermit->santri->user->fullname : '-' }}</h6>
                   <small>{{ $myPermit->presence->name }}</small>
                 </td>
                 <td>
                   <b><small>[{{ ucfirst($myPermit->reason_category) }}]</small></b>
                   <br>
                   {{ ucfirst(substr($myPermit->reason,0,30)) }}...
                 </td>
                 <td>
                   <span class="badge {{ $myPermit->status == 'pending' ? 'bg-secondary' : ($myPermit->status == 'approved' ? 'bg-success' : ($myPermit->status == 'rejected' ? 'bg-danger' : '')) }}">{{ ucwords($myPermit->status) }}</span>
                   <br>
                   <small>{{ $myPermit->updated_at }}</small>
                 </td>
                 <td class="text-center">
                   @if($myPermit->status!='rejected')
                   <a href="{{ route('edit presence permit') }}?presenceId={{ $myPermit->fkPresence_id }}" class="btn btn-primary btn-sm mb-0">Edit</a>
                   @endif
                   <a href="{{ route('delete my presence permit') }}?presenceId={{ $myPermit->fkPresence_id }}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                 </td>
               </tr>
               @endforeach
             </tbody>
           </table>
         </div>
       </div>

       <div class="tab-pane fade show" id="nav-berjangka" role="tabpanel" aria-labelledby="nav-berjangka-tab">
         <div class="card-header justify-content-between align-items-center">
           <a href="{{ (auth()->user()->hasRole('superadmin')) ? route('create presence permit') : route('ranged presence permit submission') }}" class="btn btn-primary btn-block btn-rounded mb-2 mt-2">
             <i class="fas fa-plus" aria-hidden="true"></i>
             Buat izin berjangka
           </a>
         </div>
         <div class="datatable table-responsive p-2">
           <table id="table-generator" class="table align-items-center mb-0">
             <thead>
               <tr>
                 <th class="text-uppercase text-secondary font-weight-bolder">Nama</th>
                 <th class="text-uppercase text-secondary font-weight-bolder ps-2">Alasan</th>
                 <th class="text-uppercase text-secondary font-weight-bolder ps-2">Tanggal</th>
                 <th class="text-center text-uppercase text-secondary font-weight-bolder">Action</th>
               </tr>
             </thead>
             <tbody>
               @isset($myRangedPermits)
               @foreach($myRangedPermits as $myRangedPermit)
               <tr class="text-sm">
                 <td>
                   <h6 class="mb-0 text-sm">{{ ($myRangedPermit->santri) ? $myRangedPermit->santri->user->fullname : '-' }}</h6>
                   {{ isset($myRangedPermit->presenceGroup) ? $myRangedPermit->presenceGroup->name : '' }}
                 </td>
                 <td>
                   <b><small>[{{ ucfirst($myRangedPermit->reason_category) }}]</small></b>
                   <br>
                   {{ $myRangedPermit->reason }}
                 </td>
                 <td><small>
                     <span class="badge {{ $myRangedPermit->status == 'pending' ? 'bg-secondary' : ($myRangedPermit->status == 'approved' ? 'bg-success' : ($myRangedPermit->status == 'rejected' ? 'bg-danger' : '')) }}">{{ ucwords($myRangedPermit->status) }}</span>
                     <br>
                     {{ $myRangedPermit->from_date }} s.d {{ $myRangedPermit->to_date }}
                   </small></td>
                 <td class="align-middle text-center text-sm">
                   <a href="{{ route('delete my ranged presence permit', $myRangedPermit->id) }}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                 </td>
               </tr>
               @endforeach
               @endisset
             </tbody>
           </table>
         </div>
       </div>
     </div>
   </nav>
 </div>

 <script>
   try {
     $(document).ready();
   } catch (e) {
     window.location.replace(`{{ url("/") }}`)
   }

   //  $('#table-izin').DataTable({
   //    order: [
   //      // [1, 'desc']
   //    ],
   //    pageLength: 25
   //  });

   //  $('#table-generator').DataTable({
   //    order: [
   //      // [1, 'desc']
   //    ],
   //    pageLength: 25
   //  });
 </script>