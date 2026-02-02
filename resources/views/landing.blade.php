@extends('layouts.landing')

@section('title', 'MarketLink - التسويق الرقمي والتجارة الإلكترونية بالذكاء الاصطناعي')

@section('content')
    {{-- Hero --}}
    <section class="py-16 md:py-24 px-4">
        <div class="container mx-auto max-w-6xl text-center">
            <h1 class="text-3xl md:text-5xl font-bold text-slate-800 mb-4 leading-tight">
                تسويق رقمي وتجارة إلكترونية
                <span class="text-indigo-600">بالذكاء الاصطناعي</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto">
                شركة متخصصة في دعم من يبيعون أونلاين. نغيّر شكل أعمالكم بالأدوات الجديدة المتاحة.
            </p>
        </div>
    </section>

    {{-- الخدمات --}}
    <section class="py-16 px-4 bg-white">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 text-center mb-12">خدماتنا بالذكاء الاصطناعي</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {{-- 1. محتوى مرئي --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">🎬</div>
                    <h3 class="font-bold text-slate-800 mb-2">فيديوهات كارتون</h3>
                    <p class="text-slate-600 text-sm">إنتاج فيديوهات كارتون جذابة لمنتجاتك وعلامتك باستخدام الذكاء الاصطناعي.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">📱</div>
                    <h3 class="font-bold text-slate-800 mb-2">فيديوهات UGC</h3>
                    <p class="text-slate-600 text-sm">فيديوهات محتوى مستخدم احترافية مناسبة للسوشيال ميديا والإعلانات.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">✨</div>
                    <h3 class="font-bold text-slate-800 mb-2">تصميمات وأفكار إبداعية</h3>
                    <p class="text-slate-600 text-sm">تصميمات جرافيك وأفكار إبداعية لتمييز علامتك باستخدام أدوات الذكاء الاصطناعي.</p>
                </div>
                {{-- 2. حملات وتسويق --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">📢</div>
                    <h3 class="font-bold text-slate-800 mb-2">حملات إعلانية ضخمة</h3>
                    <p class="text-slate-600 text-sm">إنشاء وإدارة حملات إعلانية ضخمة تصل لجمهورك المستهدف وتزيد المبيعات.</p>
                </div>
                {{-- 3. أنظمة وخدمات --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">💬</div>
                    <h3 class="font-bold text-slate-800 mb-2">CRM رد على العملاء</h3>
                    <p class="text-slate-600 text-sm">أنظمة إدارة علاقات العملاء والرد على العملاء بشكل منظم واحترافي.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">🛒</div>
                    <h3 class="font-bold text-slate-800 mb-2">أنظمة إلكترونية</h3>
                    <p class="text-slate-600 text-sm">حلول تجارة إلكترونية ومواقع متكاملة مدعومة بالذكاء الاصطناعي.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">🎮</div>
                    <h3 class="font-bold text-slate-800 mb-2">ألعاب إلكترونية</h3>
                    <p class="text-slate-600 text-sm">تصميم وتطوير ألعاب إلكترونية تفاعلية لتسويق علامتك أو منتجاتك بطريقة ممتعة.</p>
                </div>
            </div>
        </div>
    </section>

@endsection
