<button type="button" @click="switchLanguage(locale === 'nl' ? 'en' : 'nl')" class="relative inline-flex items-center justify-between w-14 h-7 p-1 rounded-full bg-slate-900 border border-slate-800 cursor-pointer" aria-label="Language">
    <span class="text-[9px] font-bold z-10 pl-1" :class="locale === 'en' ? 'text-slate-900' : 'text-slate-500'">EN</span>
    <span class="text-[9px] font-bold z-10 pr-1" :class="locale === 'nl' ? 'text-slate-900' : 'text-slate-500'">NL</span>
    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 transition-transform duration-300" :class="locale === 'nl' ? 'translate-x-7' : ''"></span>
</button>
