@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')
            <div class="welcome-section">
                <h2>مرحباً بك في ماركت لينك</h2>
                <p>نظام إدارة بيانات العملاء</p>
            </div>

            <div class="dashboard-content">
        <div class="info-card clickable-card" id="registrationCard">
            <h3>بيانات التسجيل</h3>
            <p>اضغط لعرض بيانات التسجيل للعملاء</p>
            <button class="btn-primary">عرض البيانات</button>
        </div>

                <div class="info-card">
                    <h3>إدارة العملاء</h3>
                    <p>يمكنك هنا إدارة بيانات العملاء والتسجيلات</p>
                    <button class="btn-primary">إضافة عميل جديد</button>
                </div>
            </div>
@endsection

@push('modals')
    <!-- Modal لعرض بيانات التسجيل -->
    <div id="registrationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>بيانات التسجيل</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="loading" class="loading">جاري التحميل...</div>
                <div id="customersData" class="customers-data"></div>
                <div id="noData" class="no-data" style="display: none;">
                    <p>لا توجد بيانات تسجيل متاحة</p>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // فتح الـ Modal عند الضغط على الكارد
        document.getElementById('registrationCard').addEventListener('click', function() {
            const modal = document.getElementById('registrationModal');
            modal.style.display = 'block';
            loadCustomersData();
        });

        // إغلاق الـ Modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('registrationModal').style.display = 'none';
        });

        // إغلاق الـ Modal عند الضغط خارج المحتوى
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('registrationModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // جلب بيانات العملاء
        function loadCustomersData() {
            const loading = document.getElementById('loading');
            const customersData = document.getElementById('customersData');
            const noData = document.getElementById('noData');

            loading.style.display = 'block';
            customersData.innerHTML = '';
            noData.style.display = 'none';

            fetch('{{ route("customers.index") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (data.length === 0) {
                    noData.style.display = 'block';
                    return;
                }

                let html = '<table class="customers-table"><thead><tr><th>#</th><th>الاسم</th><th>رقم الهاتف</th><th>الحالة</th><th>تاريخ التسجيل</th></tr></thead><tbody>';
                
                data.forEach((customer, index) => {
                    const date = new Date(customer.created_at);
                    const formattedDate = date.toLocaleDateString('ar-EG', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${customer.name}</td>
                            <td>${customer.phone}</td>
                            <td><span class="status-badge status-${customer.status}">${customer.status === 'active' ? 'نشط' : 'غير نشط'}</span></td>
                            <td>${formattedDate}</td>
                        </tr>
                    `;
                });

                html += '</tbody></table>';
                customersData.innerHTML = html;
            })
            .catch(error => {
                loading.style.display = 'none';
                customersData.innerHTML = '<p class="error">حدث خطأ أثناء جلب البيانات</p>';
                console.error('Error:', error);
            });
        }
    </script>
@endpush

