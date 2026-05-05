@extends('layouts.guest')

@section('title', 'Política de Privacidad y Tratamiento de Datos')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Encabezado --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Política de Privacidad y Tratamiento de Datos Personales</h1>
            <p class="text-sm text-slate-500 mt-1">Vigente desde el 1 de enero de 2025 · Actualizada el 5 de mayo de 2026</p>
        </div>

        <div class="card">
            <div class="card-body prose prose-slate max-w-none text-sm leading-relaxed space-y-6">

                {{-- 1 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">1. Responsable del tratamiento</h2>
                    <p>El responsable del tratamiento de sus datos personales es la organización que utiliza este sistema de evaluación psicológica para procesos de selección de personal. Para ejercer sus derechos puede comunicarse al correo electrónico habilitado para tal fin, indicado al final de este documento.</p>
                </section>

                {{-- 2 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">2. Marco legal</h2>
                    <p>El tratamiento de sus datos personales se realiza en cumplimiento de:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-700 mt-2">
                        <li><strong>Ley 1581 de 2012</strong> — Régimen General de Protección de Datos Personales.</li>
                        <li><strong>Decreto 1377 de 2013</strong> — Reglamentación parcial de la Ley 1581.</li>
                        <li><strong>Ley 1090 de 2006</strong> — Código Deontológico y Bioético del Psicólogo.</li>
                        <li><strong>Decreto 1074 de 2015</strong> — Decreto Único Reglamentario del Sector Comercio.</li>
                    </ul>
                </section>

                {{-- 3 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">3. Datos que recopilamos</h2>
                    <p>En el marco del proceso de selección recopilamos únicamente los datos necesarios para evaluar su perfil profesional:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-700 mt-2">
                        <li>Datos de identificación: nombre completo, número de documento, correo electrónico y teléfono.</li>
                        <li>Datos del proceso: cargo al que aplica, respuestas a las pruebas psicotécnicas, resultados y perfil psicológico.</li>
                        <li>Datos técnicos: dirección IP y user-agent al momento de otorgar el consentimiento informado.</li>
                    </ul>
                    <p class="mt-2"><strong>No recopilamos</strong> datos sensibles como origen racial, orientación sexual, creencias religiosas ni datos de salud ajenos al proceso de selección.</p>
                </section>

                {{-- 4 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">4. Finalidad del tratamiento</h2>
                    <p>Sus datos son tratados exclusivamente para:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-700 mt-2">
                        <li>Evaluar su idoneidad para el cargo al que aplica.</li>
                        <li>Generar el informe psicológico utilizado por el equipo de selección.</li>
                        <li>Cumplir con obligaciones legales en materia laboral y de protección de datos.</li>
                    </ul>
                    <p class="mt-2">Sus datos <strong>no serán vendidos, cedidos ni compartidos</strong> con terceros no vinculados al proceso de selección.</p>
                </section>

                {{-- 5 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">5. Tiempo de conservación</h2>
                    <p>Sus datos personales y resultados de las pruebas serán conservados durante <strong>2 (dos) años</strong> contados a partir de la finalización del proceso de selección, período tras el cual serán eliminados de forma segura, salvo obligación legal en contrario.</p>
                </section>

                {{-- 6 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">6. Sus derechos (Habeas Data)</h2>
                    <p>De conformidad con la Ley 1581 de 2012, usted tiene derecho a:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-700 mt-2">
                        <li><strong>Conocer</strong> los datos personales que poseemos sobre usted.</li>
                        <li><strong>Actualizar y rectificar</strong> sus datos cuando sean inexactos o estén desactualizados.</li>
                        <li><strong>Solicitar la supresión</strong> de sus datos cuando no sean necesarios para la finalidad que justificó su recolección.</li>
                        <li><strong>Revocar la autorización</strong> de tratamiento en cualquier momento.</li>
                        <li><strong>Acceder gratuitamente</strong> a sus datos personales al menos una vez al mes.</li>
                        <li><strong>Presentar quejas</strong> ante la Superintendencia de Industria y Comercio (SIC) por infracciones a la normativa de protección de datos.</li>
                    </ul>
                </section>

                {{-- 7 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">7. Cómo ejercer sus derechos</h2>
                    <p>Para ejercer cualquiera de sus derechos, puede:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-700 mt-2">
                        <li>Ingresar al portal del candidato y usar la opción <strong>"Solicitar eliminación de mis datos"</strong>.</li>
                        <li>Enviar una solicitud escrita al correo electrónico del responsable del tratamiento.</li>
                    </ul>
                    <p class="mt-2">Su solicitud será atendida en un plazo máximo de <strong>15 días hábiles</strong> conforme a lo establecido en la Ley 1581 de 2012.</p>
                </section>

                {{-- 8 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">8. Seguridad de los datos</h2>
                    <p>Implementamos medidas técnicas y organizativas para proteger sus datos personales contra acceso no autorizado, pérdida o divulgación indebida, incluyendo control de acceso por roles, autenticación de dos factores y comunicaciones cifradas (HTTPS).</p>
                </section>

                {{-- 9 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">9. Uso de inteligencia artificial</h2>
                    <p>El sistema puede utilizar herramientas de inteligencia artificial para generar <strong>borradores de narrativa</strong> como apoyo al psicólogo evaluador. Estos textos son revisados, validados y firmados por un profesional habilitado. La IA <strong>no toma decisiones autónomas</strong> sobre su proceso de selección; la responsabilidad de la evaluación recae exclusivamente en el psicólogo responsable.</p>
                </section>

                {{-- 10 --}}
                <section>
                    <h2 class="text-base font-bold text-slate-800 mb-2">10. Contacto</h2>
                    <p>Para cualquier consulta relacionada con el tratamiento de sus datos personales, puede escribirnos a:</p>
                    <p class="mt-2 font-medium text-slate-800">datos@empresa.com</p>
                    <p class="text-slate-500 text-xs mt-1">Reemplace esta dirección con el correo oficial de la organización responsable del tratamiento.</p>
                </section>

            </div>
        </div>

        <div class="mt-6 text-center space-y-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm">← Volver al inicio</a>
            @else
                <a href="{{ route('candidate.access') }}" class="btn-ghost btn-sm">← Portal de candidatos</a>
            @endauth
            <p class="text-xs text-slate-400">Superintendencia de Industria y Comercio · <a href="https://www.sic.gov.co" target="_blank" class="underline hover:text-slate-600">www.sic.gov.co</a></p>
        </div>

    </div>
</div>
@endsection
