@extends('layouts.landing')

@section('title', 'ماركت لينك - التسويق الرقمي والتجارة الإلكترونية بالذكاء الاصطناعي')

@section('content')
    {{-- Hero --}}
    <section class="landing-hero relative py-20 md:py-28 px-4 overflow-hidden">
        <div class="landing-hero-bg" aria-hidden="true"></div>
        <div class="container mx-auto max-w-6xl text-center relative">
            <h1 class="text-3xl md:text-5xl font-bold text-slate-800 mb-8 leading-snug">
                نوفر
                <span class="text-indigo-600">حلولاً تسويقية رقمية شاملة</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-8 leading-relaxed">
                نقلة نوعية في تجربتك في التسويق — نوصلك بعملائك.
            </p>
            <p class="text-base md:text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed">
                الفكرة الأساسية عندنا: <strong class="text-slate-700">الإبداع والتميز</strong>، واستخدام أدوات <strong class="text-indigo-600">الذكاء الاصطناعي</strong> للوصول إلى أكبر عدد من العملاء.
            </p>
        </div>
    </section>

    {{-- الخدمات --}}
    <section class="landing-services py-20 md:py-28 px-4">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-800 text-center mb-16">خدماتنا بالذكاء الاصطناعي</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                {{-- 1. محتوى مرئي --}}
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="video" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">فيديوهات كارتون</h3>
                    <p class="text-slate-600 text-base leading-relaxed">إنتاج فيديوهات كارتون جذابة لمنتجاتك وعلامتك باستخدام الذكاء الاصطناعي.</p>
                </div>
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="play" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">فيديوهات UGC</h3>
                    <p class="text-slate-600 text-base leading-relaxed">فيديوهات محتوى مستخدم احترافية مناسبة للسوشيال ميديا والإعلانات.</p>
                </div>
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="sparkles" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">تصميمات وأفكار إبداعية</h3>
                    <p class="text-slate-600 text-base leading-relaxed">تصميمات جرافيك وأفكار إبداعية لتمييز علامتك باستخدام أدوات الذكاء الاصطناعي.</p>
                </div>
                {{-- 2. حملات وتسويق --}}
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="megaphone" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">حملات إعلانية ضخمة</h3>
                    <p class="text-slate-600 text-base leading-relaxed">إنشاء وإدارة حملات إعلانية ضخمة تصل لجمهورك المستهدف وتزيد المبيعات.</p>
                </div>
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="globe" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">إدارة وسائل التواصل الاجتماعي</h3>
                    <p class="text-slate-600 text-base leading-relaxed">إدارة حساباتك على فيسبوك، إنستغرام، تيك توك وغيرها ونشر محتوى يجذب جمهورك.</p>
                </div>
                {{-- 3. أنظمة وخدمات --}}
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="chat" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">CRM رد على العملاء</h3>
                    <p class="text-slate-600 text-base leading-relaxed">أنظمة إدارة علاقات العملاء والرد على العملاء بشكل منظم واحترافي.</p>
                </div>
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="cart" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">أنظمة إلكترونية</h3>
                    <p class="text-slate-600 text-base leading-relaxed">حلول تجارة إلكترونية ومواقع متكاملة مدعومة بالذكاء الاصطناعي.</p>
                </div>
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="device-phone" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">تطبيقات الهواتف الذكية</h3>
                    <p class="text-slate-600 text-base leading-relaxed">تصميم وتطوير تطبيقات أندرويد وآيفون لعلامتك أو متجرك لوصول أسهل للعملاء.</p>
                </div>
                <div class="landing-service-card p-8 rounded-2xl bg-white border border-slate-200 border-t-4 border-t-indigo-500 hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                    <div class="landing-service-icon mb-6">
                        <x-landing-icons name="puzzle" />
                    </div>
                    <h3 class="font-bold text-slate-800 mb-4 text-lg">ألعاب إلكترونية</h3>
                    <p class="text-slate-600 text-base leading-relaxed">تصميم وتطوير ألعاب إلكترونية تفاعلية لتسويق علامتك أو منتجاتك بطريقة ممتعة.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- نبتكر، نطور، نحسن ونبدع --}}
    <section class="landing-cta py-20 md:py-28 px-4 bg-white border-t-4 border-indigo-100">
        <div class="container mx-auto max-w-4xl text-center px-6">
            <p class="text-lg md:text-xl text-slate-700 leading-loose">
                نبتكر، نطور، نحسن ونبدع لمساعدتك على نمو مشروعك وزيادة أرباحك والاستمرارية في تحقيق أهدافك وأرباحك عن طريق فرق متخصصة في التسويق والتجارة الإلكترونية.
            </p>
        </div>
    </section>

@endsection

@push('styles')
<style>
/* Hero: gradient + decorative shape */
.landing-hero { position: relative; background: linear-gradient(160deg, #f8fafc 0%, #eef2ff 45%, #e0e7ff 100%); }
.landing-hero-bg {
    position: absolute; inset: 0; overflow: hidden; pointer-events: none;
    background: radial-gradient(ellipse 80% 50% at 70% 20%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 20% 80%, rgba(129, 140, 248, 0.06) 0%, transparent 50%);
}
/* Services section: subtle alternate background */
.landing-services { background: linear-gradient(180deg, #f1f5f9 0%, #f8fafc 100%); }
/* Cards: icon size + hover */
.landing-service-icon svg { width: 4rem; height: 4rem; color: rgb(99 102 241); transition: color 0.2s ease, transform 0.2s ease; }
.landing-service-card:hover .landing-service-icon svg { color: rgb(79 70 229); transform: scale(1.08); }
</style>
@endpush
