/*
    Vanilla AutoComplete v0.1
    Copyright (c) 2019 Mauro Marssola
    GitHub: https://github.com/marssola/vanilla-calendar
    License: http://www.opensource.org/licenses/mit-license.php
*/
// Récupérer la langue du navigateur 
const userLang =  navigator.language || navigator.userLanguage;
const StartOfWeek = {
  'ar-BH': 6,
  'ar-DZ': 6,
  'ar-JO': 6,
  'ar-KW': 6,
  'ar-LB': 6,
  'ar-LY': 6,
  'ar-MA': 6,
  'ar-OM': 6,
  'ar-QA': 6,
  'ar-SA': 6,
  'ar-SD': 6,
  'ar-SY': 6,
  'ar-TN': 6,
  'ar-AE': 6,
  'ar-YE': 6,
  'ca-ES': 1,
  'ca': 1,
  'cs-CZ': 1,
  'cs': 1,
  'da_DK': 1,
  'da': 1,
  'de-AT': 1,
  'de-DE': 1,
  'de-LU': 1,
  'de-CH': 1,
  'de': 1,
  'el-GR': 1,
  'el': 1,
  'en_GB': 1,
  'en-IE': 1,
  'es-AR': 1,
  'es-BO': 1,
  'es-CL': 1,
  'es-CO': 1,
  'es-CR': 1,
  'es-DO': 1,
  'es-EC': 1,
  'es-SV': 1,
  'es-GT': 1,
  'es-HN': 1,
  'es-MX': 1,
  'es-NI': 1,
  'es-PA': 1,
  'es-PY': 1,
  'es-PE': 1,
  'es-PR': 1,
  'es-ES': 1,
  'es-UY': 1,
  'es-VE': 1,
  'et-EE': 1,
  'et': 1,
  'fi-FI': 1,
  'fi': 1,
  'fr-BE': 1,
  'fr-KM': 1,
  'fr-FR': 1,
  'fr-GN': 1,
  'fr-LU': 1,
  'fr-MC': 1,
  'fr-CH': 1,
  'fr': 1,
  'hr-HR': 1,
  'hr': 1,
  'hu-HU': 1,
  'hu': 1,
  'id-ID': 1,
  'is-IS': 1,
  'is': 1,
  'it-IT': 1,
  'it-CH': 1,
  'it': 1,
  'lt-LT': 1,
  'lt': 1,
  'nl-BE': 1,
  'nl-NL': 1,
  'nl': 1,
  'nb-NO': 1,
  'nb': 1,
  'nn-NO': 1,
  'nn': 1,
  'pl-PL': 1,
  'pl': 1,
  'pt-MZ': 1,
  'pt-PT': 1,
  'pt': 1,
  'ro-MD': 1,
  'ro-RO': 1,
  'ro': 1,
  'sq-AL': 6,
  'ru-MD': 1,
  'ru-RU': 1,
  'ru-UA': 1,
  'ru': 1,
  'sk-SK': 1,
  'sk': 1,
  'sl-SI': 1,
  'sl': 1,
  'sr-Cyrl': 1,
  'sr-Cyrl-BA': 1,
  'sr-Cyrl-ME': 1,
  'sr-Cyrl-RS': 1,
  'sr-Latn': 1,
  'sr-Latn-BA': 1,
  'sr-Latn-ME': 1,
  'sr-Latn-RS': 1,
  'sr': 1,
  'sr-BA': 1,
  'sr-ME': 1,
  'sr-RS': 1,
  'sv-FI': 1,
  'sv-SE': 1,
  'sv': 1,
  'tr-TR': 1,
  'tr': 1,
  'uk-UA': 1,
  'uk': 1,
};
//           6//    6;//
const firstDay=    StartOfWeek[userLang] || 0; 
const secondDay = function(){
    if(firstDay){}
};
// Créer un tableau des mois dynamiquement en fonction de la langue du navigateur
const months = [];
for (let i = 0; i < 12; i++) {
  const date = new Date(2021, i, 1);  // Peu importe l'année et le jour ici
  const monthName = new Intl.DateTimeFormat(userLang, { month: 'long' }).format(date);
  months.push(monthName);
}
function shiftDaysToUserLang(days) {
    if(firstDay !==0) {
  for(let i=0; i<firstDay+12;i++) {
   days.splice(0, 0, days[6]);
    days.pop()
  }
    
  }
  console.log(days);
}
// Créer un tableau des jours de la semaine dynamiquement en fonction de la langue du navigateur
const shortWeekday = [];
for (let i = 0; i < 7; i++) {
  const date = new Date(2021, 0, i + 3);  // Le 3 janvier 2021 est un dimanche, donc on commence de là
  const dayName = new Intl.DateTimeFormat(userLang, { weekday: 'short' }).format(date);
  shortWeekday.push(dayName);
}
shiftDaysToUserLang(shortWeekday);
const longWeekday = [];
for (let i = 0; i < 7; i++) {
  const date = new Date(2021, 0, i + 3);  // we start from a sunday
  const dayName = new Intl.DateTimeFormat(userLang, { weekday: 'long' }).format(date);
  longWeekday.push(dayName);
}
 shiftDaysToUserLang(longWeekday);

let VanillaCalendar = (function () {
    function VanillaCalendar(options) {
        function addEvent(el, type, handler){
            if (!el) return
            if (el.attachEvent) el.attachEvent('on' + type, handler)
            else el.addEventListener(type, handler);
        }
        function removeEvent(el, type, handler){
            if (!el) return
            if (el.detachEvent) el.detachEvent('on' + type, handler)
            else el.removeEventListener(type, handler);
        }
        let opts = {
            selector: null,
            datesFilter: false,
            pastDates: true,
            availableWeekDays: [],
            availableDates: [],
            date: new Date(),
            todaysDate: new Date(),
            button_prev: null,
            button_next: null,
            month: null,
            month_label: null,
            onSelect: (data, elem) => {},
            months,
            shortWeekday,
        }
        for (let k in options) if (opts.hasOwnProperty(k)) opts[k] = options[k]
        
        let element = document.querySelector(opts.selector)
        if (!element)
            return
        
        const getWeekDay = function (day) {
            return longWeekday[day]
        }

        const createDay = function (date) {
            let newDayElem = document.createElement('div')
            
            let dateElem = document.createElement('span')
            dateElem.innerHTML = date.getDate()
            newDayElem.className = 'vanilla-calendar-date'
            newDayElem.setAttribute('data-calendar-date', date)
            if(date.getDay() == 0 || date.getDay()==6) newDayElem.classList.add('we')
            let available_week_day = opts.availableWeekDays.filter(f => f.day === date.getDay() || f.day === getWeekDay(date.getDay()))
            let available_date = opts.availableDates.filter(f => f.date === (date.getFullYear() + '-' + String(date.getMonth() + 1).padStart('2', 0) + '-' + String(date.getDate()).padStart('2', 0)))
            
            if (date.getDate() === 1) {
            thedate =Number(date.getDay());
            if(Number(date.getDay()+7)<= (6+firstDay) ) this.dayPos= Number((date.getDay()+7)- firstDay);
            else this.dayPos= Number(date.getDay()-firstDay)
            newDayElem.style.marginLeft = ((this.dayPos ) * 14.28) + '%';

            }
            if (opts.date.getTime() <= opts.todaysDate.getTime() - 1 && !opts.pastDates) {
                newDayElem.classList.add('vanilla-calendar-date--disabled')
            } else {
                if (opts.datesFilter) {
                    if (available_week_day.length) {
                        newDayElem.classList.add('vanilla-calendar-date--active')
                        newDayElem.setAttribute('data-calendar-data', JSON.stringify(available_week_day[0]))
                        newDayElem.setAttribute('data-calendar-status', 'active')
                    } else if (available_date.length) {
                        newDayElem.classList.add('vanilla-calendar-date--active')
                        newDayElem.setAttribute('data-calendar-data', JSON.stringify(available_date[0]))
                        newDayElem.setAttribute('data-calendar-status', 'active')
                    } else {
                        newDayElem.classList.add('vanilla-calendar-date--disabled')
                    }
                } else {
                    newDayElem.classList.add('vanilla-calendar-date--active')
                    newDayElem.setAttribute('data-calendar-status', 'active')
                }
            }
            if (date.toString() === opts.todaysDate.toString()) {
                newDayElem.classList.add('vanilla-calendar-date--today')
            }
            
            newDayElem.appendChild(dateElem)
            opts.month.appendChild(newDayElem)
        }
        
        const removeActiveClass = function () {
            document.querySelectorAll('.vanilla-calendar-date--selected').forEach(s => {
                s.classList.remove('vanilla-calendar-date--selected')
            })
        }
        
        const selectDate = function () {
            let activeDates = element.querySelectorAll('[data-calendar-status=active]')
            activeDates.forEach(date => {

                let datas = date.dataset
                let data = {}
                if (datas.calendarData) {
                data.hover = JSON.parse(datas.calendarData);
                date.insertAdjacentHTML('beforeend',data.hover.hover);
                }

                date.addEventListener('click', function () {
                    removeActiveClass()
                    let datas = this.dataset
                    let data = {}
                    if (datas.calendarDate)
                        data.date = datas.calendarDate
                    if (datas.calendarData)
                        data.data = JSON.parse(datas.calendarData)
                    opts.onSelect(data, this)
                    this.classList.add('vanilla-calendar-date--selected')
                })
            })
        }
        
        const createMonth = function () {
            clearCalendar()
            let currentMonth = opts.date.getMonth()
            while (opts.date.getMonth() === currentMonth) {
                createDay(opts.date)
                opts.date.setDate(opts.date.getDate() + 1)
            }
            
            opts.date.setDate(1)
            opts.date.setMonth(opts.date.getMonth() -1)
            opts.month_label.innerHTML = opts.months[opts.date.getMonth()] + ' ' + opts.date.getFullYear()
            selectDate()
        }
        
        const monthPrev = function () {
            opts.date.setMonth(opts.date.getMonth() - 1)
            createMonth()
            show.innerHTML='';
        }
        
        const monthNext = function () {
            opts.date.setMonth(opts.date.getMonth() + 1)
            createMonth()
            show.innerHTML='';
        }
        
        const clearCalendar = function () {
            opts.month.innerHTML = ''
        }
        
        const createCalendar = function () {
            document.querySelector(opts.selector).innerHTML = `
            <div class="vanilla-calendar-header">
                <button type="button" class="vanilla-calendar-btn" data-calendar-toggle="previous"><svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"></path></svg></button>
                <div class="vanilla-calendar-header__label" data-calendar-label="month"></div>
                <button type="button" class="vanilla-calendar-btn" data-calendar-toggle="next"><svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"></path></svg></button>
            </div>
            <div class="vanilla-calendar-week"></div>
            <div class="vanilla-calendar-body" data-calendar-area="month"></div>
            `;
            document.querySelector(opts.selector).setAttribute('lang',userLang);
        }
        const setWeekDayHeader = function () {
            document.querySelector(`${opts.selector} .vanilla-calendar-week`).innerHTML = `
                <span>${opts.shortWeekday[0]}</span>
                <span>${opts.shortWeekday[1]}</span>
                <span>${opts.shortWeekday[2]}</span>
                <span>${opts.shortWeekday[3]}</span>
                <span>${opts.shortWeekday[4]}</span>
                <span>${opts.shortWeekday[5]}</span>
                <span>${opts.shortWeekday[6]}</span>
            `
        }
        
        this.init = function () {
            createCalendar()
            opts.button_prev = document.querySelector(opts.selector + ' [data-calendar-toggle=previous]')
            opts.button_next = document.querySelector(opts.selector + ' [data-calendar-toggle=next]')
            opts.month = document.querySelector(opts.selector + ' [data-calendar-area=month]')
            opts.month_label = document.querySelector(opts.selector + ' [data-calendar-label=month]')
            
            opts.date.setDate(1)
            createMonth()
            setWeekDayHeader()
            addEvent(opts.button_prev, 'click', monthPrev)
            addEvent(opts.button_next, 'click', monthNext)
        }
        
        this.destroy = function () {
            removeEvent(opts.button_prev, 'click', monthPrev)
            removeEvent(opts.button_next, 'click', monthNext)
            clearCalendar()
        }
        
        this.reset = function () {
            this.destroy()
            this.init()
        }
        
        this.set = function (options) {
            for (let k in options)
                if (opts.hasOwnProperty(k))
                    opts[k] = options[k]
            createMonth()
//             this.reset()
        }
        
        this.init()
    }
    
    return VanillaCalendar
})()

window.VanillaCalendar = VanillaCalendar
