<div class="min-h-screen bg-[#FAF7EF] dark:bg-[#0E1A14] px-6 py-14" style="font-family: 'Inter', sans-serif;">
    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                 alt="CSAV logo"
                 class="w-12 h-12 object-contain">
            <div>
                <h1 class="text-[#123524] dark:text-green-200" style="font-family: 'Fraunces', serif; font-size: 1.9rem; font-weight: 700;">
                    Patakaran sa Privacy
                </h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400">
                    Huling na-update noong {{ now()->format('F d, Y') }}
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#16281F] border border-[#E4DFCE] dark:border-[#1C3B2E] rounded-2xl shadow-lg p-8 sm:p-10 space-y-8 text-gray-700 dark:text-neutral-300 leading-relaxed">

            <p>
                Ang Colegio De Sta. Ana de Victorias, Inc. (“CSAV,” “kami,” o “amin”) ay may pananagutan sa
                pagprotekta ng iyong personal na impormasyon. Ang Patakarang ito sa Privacy ay naglalarawan kung
                paano namin kinokolekta, ginagamit, iniimbak, at pinoprotektahan ang iyong data kapag gumagamit ka
                ng sistemang ito para sa pag-reserve ng pasilidad at paghiling ng materyales. Sinusunod namin ang
                mga prinsipyo ng <strong>General Data Protection Regulation (GDPR)</strong> at ng
                <strong>Republic Act No. 10173 (Data Privacy Act of 2012)</strong> ng Pilipinas bilang gabay sa
                responsableng paghawak ng personal na datos.
            </p>

            {{-- 1 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">1. Impormasyong Aming Kinokolekta</h2>
                <p class="mb-2">Kapag ikaw ay nagparehistro o gumamit ng sistema, maaari naming kolektahin ang mga sumusunod:</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Buong pangalan</li>
                    <li>Email address</li>
                    <li>Tungkulin (Student, Faculty, o Program Head) at Departamento</li>
                    <li>Password (naka-encrypt at hindi kailanman iniimbak sa plain text)</li>
                    <li>Mga detalye ng iyong mga request — halimbawa, reserbasyon ng pasilidad, hiniling na materyales, petsa, oras, at layunin</li>
                    <li>Mga log ng aktibidad para sa audit at seguridad ng sistema</li>
                </ul>
            </section>

            {{-- 2 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">2. Layunin ng Pagproseso ng Data</h2>
                <p class="mb-2">Ginagamit namin ang iyong impormasyon para lamang sa mga sumusunod na layunin:</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Paglikha at pamamahala ng iyong account</li>
                    <li>Pagproseso ng iyong mga hiling para sa pasilidad o materyales</li>
                    <li>Pakikipag-ugnayan tungkol sa katayuan ng iyong request (approval, rejection, atbp.)</li>
                    <li>Pagpapanatili ng talaan para sa pananagutan, imbentaryo, at audit</li>
                    <li>Pagpapabuti ng seguridad at functionality ng sistema</li>
                </ul>
            </section>

            {{-- 3 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">3. Legal na Basehan ng Pagproseso</h2>
                <p>
                    Prinoproseso namin ang iyong data batay sa: (a) iyong pahintulot (consent) sa oras ng
                    pagparehistro; (b) ang pangangailangang matupad ang isang kasunduan o serbisyong hiniling mo
                    (halimbawa, ang pagproseso ng iyong reservation); at (c) ang lehitimong interes ng CSAV na
                    mapanatili ang tamang paggamit at seguridad ng mga pasilidad at materyales ng institusyon.
                </p>
            </section>

            {{-- 4 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">4. Pagbabahagi ng Impormasyon</h2>
                <p>
                    Hindi namin ibinebenta o ipinapamahagi ang iyong personal na data sa mga third party para sa
                    layuning pangmarketing. Ang iyong impormasyon ay maaaring makita lamang ng mga awtorisadong
                    tauhan ng CSAV (tulad ng administrator, coordinator, o program head) na may lehitimong
                    pangangailangan na ma-access ito bilang bahagi ng proseso ng approval o pamamahala ng
                    request.
                </p>
            </section>

            {{-- 5 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">5. Pagpapanatili ng Data (Data Retention)</h2>
                <p>
                    Iimbakin namin ang iyong personal na impormasyon hangga't aktibo ang iyong account o
                    kinakailangan para sa mga layuning nakasaad sa Patakarang ito, maliban kung hilingin mong
                    burahin ito nang mas maaga at walang legal na obligasyon na pumipigil dito (halimbawa, mga
                    talaan ng audit na kinakailangang panatilihin para sa institutional records).
                </p>
            </section>

            {{-- 6 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">6. Ang Iyong Mga Karapatan</h2>
                <p class="mb-2">Alinsunod sa mga prinsipyo ng GDPR at ng Data Privacy Act, ikaw ay may karapatang:</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li><strong>Ma-access</strong> ang personal na impormasyong hawak namin tungkol sa iyo</li>
                    <li><strong>Iwasto (Rectification)</strong> ang anumang mali o hindi kumpletong impormasyon</li>
                    <li><strong>Hilingin ang pagbura (Erasure)</strong> ng iyong data, kung saan naaangkop</li>
                    <li><strong>Limitahan (Restriction)</strong> ang pagproseso ng iyong data sa ilang partikular na sitwasyon</li>
                    <li><strong>Tumutol (Object)</strong> sa pagproseso batay sa lehitimong interes</li>
                    <li><strong>Hilingin ang portability</strong> ng iyong data sa isang machine-readable na format</li>
                    <li><strong>Bawiin (Withdraw)</strong> ang iyong pahintulot anumang oras, nang hindi apektado ang legalidad ng pagprosesong ginawa bago ang pagbawi</li>
                </ul>
                <p class="mt-2">
                    Upang gamitin ang alinman sa mga karapatang ito, maaari kang makipag-ugnayan sa amin gamit
                    ang impormasyon sa ibaba.
                </p>
            </section>

            {{-- 7 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">7. Seguridad ng Data</h2>
                <p>
                    Nagpapatupad kami ng mga naaangkop na teknikal at organisasyonal na hakbang — tulad ng
                    pag-encrypt ng password, restricted na access batay sa tungkulin (role-based access), at
                    regular na pagmo-monitor — upang protektahan ang iyong personal na impormasyon laban sa
                    hindi awtorisadong pag-access, pagbabago, o pagkawala.
                </p>
            </section>

            {{-- 8 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">8. Mga Menor de Edad</h2>
                <p>
                    Ang sistemang ito ay para gamitin lamang ng mga rehistradong estudyante, faculty, at
                    program head ng CSAV. Kung ikaw ay wala pang 18 taong gulang, siguraduhing mayroon kang
                    pahintulot ng iyong magulang, tagapag-alaga, o ng institusyon bago gumamit ng sistemang ito.
                </p>
            </section>

            {{-- 9 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">9. Mga Pagbabago sa Patakarang Ito</h2>
                <p>
                    Maaari naming i-update ang Patakarang ito sa Privacy paminsan-minsan. Ang anumang
                    pagbabago ay ipapaskil sa pahinang ito kasama ang bagong petsa ng pag-update.
                </p>
            </section>

            {{-- 10 --}}
            <section>
                <h2 class="text-lg font-semibold text-[#123524] dark:text-green-300 mb-2">10. Makipag-ugnayan sa Amin</h2>
                <p>
                    Kung mayroon kang mga katanungan tungkol sa Patakarang ito sa Privacy o gustong gamitin ang
                    iyong mga karapatan sa data, maaari kang makipag-ugnayan sa amin sa:
                </p>
                <p class="mt-2 font-medium text-[#123524] dark:text-green-200">
                    admin@csav.edu.ph
                </p>
            </section>

            <hr class="border-[#E4E1D8] dark:border-[#2A4B3A]">

            <p class="text-xs text-gray-400 dark:text-neutral-500">
                Ang Patakarang ito ay ginawang batay sa mga prinsipyo ng
                <a href="https://gdpr-info.eu/" target="_blank" rel="noopener" class="underline hover:text-[#123524] dark:hover:text-green-300">
                    General Data Protection Regulation (GDPR)
                </a>
                at ang Republic Act No. 10173 (Data Privacy Act of 2012) ng Pilipinas.
            </p>

        </div>

        {{-- Back link --}}
        <div class="mt-8 text-center">
            <a href="/register" class="text-sm text-[#123524] dark:text-green-300 hover:underline font-medium">
                ← Bumalik
            </a>
        </div>

    </div>
</div>
