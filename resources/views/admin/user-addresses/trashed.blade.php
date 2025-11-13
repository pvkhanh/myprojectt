@extends('layouts.admin')

@section('title', 'Thùng rác - Địa chỉ người dùng')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">
                <i class="fas fa-trash-restore me-2 text-danger"></i>Thùng rác - Địa chỉ
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small text-muted mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-addresses.index') }}">Địa chỉ</a></li>
                    <li class="breadcrumb-item active">Thùng rác</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.user-addresses.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    {{-- Alert --}}
    @if($addresses->count() > 0)
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>Cảnh báo!</strong>
                    <p class="mb-0">Có <strong>{{ $addresses->total() }}</strong> địa chỉ trong thùng rác. 
                    Các địa chỉ này sẽ bị xóa vĩnh viễn nếu không được khôi phục.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-list me-2 text-primary"></i>
                Danh sách địa chỉ đã xóa
                <span class="badge bg-danger">{{ $addresses->total() }}</span>
            </h5>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Người dùng</th>
                        <th>Người nhận</th>
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th>Tỉnh/TP</th>
                        <th>Ngày xóa</th>
                        <th width="200" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($addresses as $address)
                        <tr class="bg-light">
                            <td>
                                <strong class="text-muted">{{ $address->user->username }}</strong>
                                <br><small class="text-muted">{{ $address->user->email }}</small>
                            </td>
                            <td>
                                <strong>{{ $address->receiver_name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-phone me-1"></i>{{ $address->phone }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-muted" style="max-width: 300px;">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ Str::limit($address->address, 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $address->province }}</span>
                            </td>
                            <td>
                                <small class="text-danger">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    {{ $address->deleted_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <form action="{{ route('admin.user-addresses.restore', $address->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                title="Khôi phục">
                                            <i class="fas fa-undo me-1"></i>Khôi phục
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmForceDelete({{ $address->id }})" 
                                            title="Xóa vĩnh viễn">
                                        <i class="fas fa-trash-alt me-1"></i>Xóa vĩnh viễn
                                    </button>
                                </div>
                                <form action="{{ route('admin.user-addresses.force-delete', $address->id) }}" 
                                      method="POST" class="d-none" id="forceDeleteForm{{ $address->id }}">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                <h5>Thùng rác trống</h5>
                                <p class="mb-0">Không có địa chỉ nào trong thùng rác</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Hiển thị {{ $addresses->firstItem() ?? 0 }} - {{ $addresses->lastItem() ?? 0 }}
                trong tổng số {{ $addresses->total() }} địa chỉ
            </div>
            {{ $addresses->links('components.pagination') }}
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmForceDelete(id) {
    Swal.fire({
        title: 'Xóa vĩnh viễn?',
        html: '<strong class="text-danger">CẢNH BÁO!</strong><br>Hành động này không thể hoàn tác!<br>Địa chỉ sẽ bị xóa vĩnh viễn khỏi hệ thống.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa vĩnh viễn',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Hủy',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('forceDeleteForm' + id).submit();
        }
    });
}

@if(session('success'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session("success") }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif

@if(session('error'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: '{{ session("error") }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif
</script>
@endpush

@push('styles')
<style>
    .card { border-radius: 12px; }
    tbody tr { opacity: 0.7; }
    tbody tr:hover { 
        background-color: #f1f5f9 !important; 
        opacity: 1;
    }
</style>
@endpush
@endsection