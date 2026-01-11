@extends('layouts.app')

@section('title', 'تعديل موظف')
@section('page-title', 'تعديل موظف')

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2>تعديل بيانات الموظف</h2>
            <a href="{{ route('employees.index') }}" class="btn-back">← العودة للقائمة</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="employee-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">الاسم <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $employee->name) }}" 
                    required
                    placeholder="أدخل اسم الموظف"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="phone">رقم التليفون <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $employee->phone) }}" 
                    required
                    placeholder="أدخل رقم التليفون"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label>المشاريع <span class="required">*</span></label>
                <div class="project-cards">
                    @if($projects->count() > 0)
                        @php
                            $selectedProjects = old('projects', $employee->projects->pluck('id')->toArray() ?? []);
                        @endphp
                        @foreach($projects as $project)
                            <input type="checkbox" name="projects[]" id="project_{{ $project->id }}" value="{{ $project->id }}" class="project-input" data-project-id="{{ $project->id }}" {{ in_array($project->id, $selectedProjects) ? 'checked' : '' }}>
                            <label for="project_{{ $project->id }}" class="project-card">
                                <div class="card-icon">📁</div>
                                <div class="card-title">{{ $project->name }}</div>
                                <div class="card-subtitle">{{ $project->customer->name }}</div>
                            </label>
                        @endforeach
                    @else
                        <div class="no-projects">
                            <p>لا توجد مشاريع متاحة</p>
                            <a href="{{ route('projects.create') }}" class="btn-add-small">إضافة مشروع جديد</a>
                        </div>
                    @endif
                </div>
                <small class="form-hint">يمكنك اختيار أكثر من مشروع</small>
            </div>

            <!-- Service Types for Each Project -->
            @if($projects->count() > 0)
                @foreach($projects as $project)
                    @php
                        $projectServices = [];
                        if ($employee->projects->contains($project->id)) {
                            $pivot = $employee->projects->find($project->id)->pivot;
                            $projectServices = $pivot->service_types ? json_decode($pivot->service_types, true) : [];
                        }
                        $oldServices = old("service_types.{$project->id}", $projectServices);
                    @endphp
                    <div class="form-group project-services-container" id="services_project_{{ $project->id }}" style="display: {{ in_array($project->id, $selectedProjects) ? 'block' : 'none' }};">
                        <label>الخدمات للمشروع: <strong>{{ $project->name }}</strong> <span class="required">*</span></label>
                        <div class="service-cards">
                            @php
                                $serviceTypes = [
                                    'full_management' => ['name' => 'إدارة كاملة', 'icon' => '🎯'],
                                    'media_buy' => ['name' => 'ميديا بير', 'icon' => '📢'],
                                    'design' => ['name' => 'تصميم', 'icon' => '🎨'],
                                    'videos' => ['name' => 'فيديوهات', 'icon' => '🎬'],
                                    'publishing' => ['name' => 'نشر', 'icon' => '📱'],
                                    'programming' => ['name' => 'برمجة', 'icon' => '💻'],
                                    'part_time' => ['name' => 'دوام جزئي', 'icon' => '⏰']
                                ];
                            @endphp
                            @foreach($serviceTypes as $key => $service)
                                <input type="checkbox" name="service_types[{{ $project->id }}][]" id="service_{{ $project->id }}_{{ $key }}" value="{{ $key }}" class="service-input" {{ in_array($key, $oldServices) ? 'checked' : '' }}>
                                <label for="service_{{ $project->id }}_{{ $key }}" class="service-card">
                                    <div class="card-icon">{{ $service['icon'] }}</div>
                                    <div class="card-title">{{ $service['name'] }}</div>
                                </label>
                            @endforeach
                        </div>
                        <small class="form-hint">اختر الخدمات التي سيقوم بها الموظف في هذا المشروع</small>
                    </div>
                @endforeach
            @endif

            <div class="form-group">
                <label for="monthly_salary">المرتب الشهري</label>
                <input 
                    type="number" 
                    id="monthly_salary" 
                    name="monthly_salary" 
                    value="{{ old('monthly_salary', $employee->monthly_salary) }}" 
                    step="0.01"
                    min="0"
                    placeholder="أدخل المرتب الشهري"
                    class="form-input"
                >
                <small class="form-hint">بالجنيه المصري</small>
            </div>

            <div class="form-group">
                <label for="status">الحالة <span class="required">*</span></label>
                <select id="status" name="status" required class="form-input">
                    <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">تحديث</button>
                <a href="{{ route('employees.index') }}" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
.form-container {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e8f4f8;
}

.form-header h2 {
    font-size: 28px;
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}

.btn-back {
    padding: 8px 16px;
    background: #e8f4f8;
    color: #4a90e2;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #d0e8f2;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-error {
    background-color: #ffe5e5;
    border: 1px solid #c44d4d;
    color: #c44d4d;
}

.alert-error ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.alert-error li {
    margin-bottom: 5px;
}

.employee-form {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 15px;
    font-weight: 500;
    color: #2c3e50;
}

.required {
    color: #e74c3c;
}

.form-input {
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
    color: #2c3e50;
}

.form-input:focus {
    outline: none;
    border-color: #4a90e2;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

select.form-input {
    cursor: pointer;
}

.project-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.project-input {
    display: none;
}

.project-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 140px;
}

.project-card:hover {
    border-color: #4a90e2;
    background: #f0f7fa;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
}

.project-input:checked + .project-card {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
    transform: translateY(-3px);
}

.card-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.card-title {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.card-subtitle {
    font-size: 13px;
    color: #6c7a89;
    font-weight: 400;
}

.project-input:checked + .project-card .card-title {
    color: #4a90e2;
}

.no-projects {
    text-align: center;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #e0e0e0;
}

.no-projects p {
    color: #6c7a89;
    margin-bottom: 15px;
}

.btn-add-small {
    display: inline-block;
    padding: 8px 16px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.form-hint {
    display: block;
    margin-top: 8px;
    color: #6c7a89;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.btn-submit {
    flex: 1;
    padding: 14px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.2);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}

.btn-cancel {
    flex: 1;
    padding: 14px;
    background: #e8f4f8;
    color: #5a6c7d;
    text-decoration: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #d0e8f2;
}

@media (max-width: 768px) {
    .form-container {
        padding: 20px 15px;
    }

    .form-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .form-actions {
        flex-direction: column;
    }

    .project-cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .project-card {
        padding: 15px;
        min-height: 120px;
    }

    .card-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .card-title {
        font-size: 13px;
    }
}

/* Service Cards Styles */
.service-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.service-input {
    display: none;
}

.service-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 120px;
}

.service-card:hover {
    border-color: #4a90e2;
    background: #f0f7fa;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
}

.service-input:checked + .service-card {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
    transform: translateY(-3px);
}

.service-input:checked + .service-card .card-title {
    color: #4a90e2;
}

.project-services-container {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border: 2px solid #e8f4f8;
    margin-top: 15px;
}

.project-services-container label {
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .service-cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .service-card {
        padding: 15px;
        min-height: 100px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectInputs = document.querySelectorAll('.project-input');
    
    projectInputs.forEach(function(input) {
        toggleProjectServices(input);
        input.addEventListener('change', function() {
            toggleProjectServices(this);
        });
    });
    
    function toggleProjectServices(projectInput) {
        const projectId = projectInput.getAttribute('data-project-id');
        const servicesContainer = document.getElementById('services_project_' + projectId);
        
        if (projectInput.checked) {
            servicesContainer.style.display = 'block';
        } else {
            servicesContainer.style.display = 'none';
            const serviceInputs = servicesContainer.querySelectorAll('.service-input');
            serviceInputs.forEach(function(serviceInput) {
                serviceInput.checked = false;
            });
        }
    }
    
    const form = document.querySelector('.employee-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedProjects = document.querySelectorAll('.project-input:checked');
            let isValid = true;
            
            checkedProjects.forEach(function(projectInput) {
                const projectId = projectInput.getAttribute('data-project-id');
                const servicesContainer = document.getElementById('services_project_' + projectId);
                const checkedServices = servicesContainer.querySelectorAll('.service-input:checked');
                
                if (checkedServices.length === 0) {
                    isValid = false;
                    servicesContainer.style.borderColor = '#e74c3c';
                    alert('يجب اختيار خدمة واحدة على الأقل للمشروع: ' + projectInput.closest('label').querySelector('.card-title').textContent);
                } else {
                    servicesContainer.style.borderColor = '#e8f4f8';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush

