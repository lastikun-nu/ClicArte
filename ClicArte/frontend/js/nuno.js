/*
Este parte es para traductor idiomas(Chino, Español, íngles), solo para dashboard y las paginas de los alumnos
*/

const traducciones = {
    //Tenemos 3 arrays para guardar todos textos de pagina con 3 diferentes idiomas.
    es: {

        //Parte comun, varios pagina usa este palabra.
        logintitle: "Iniciar Sesión",
        email: "Correo Electrónico",
        password: "Contraseña",
        passwordinput: "Contraseña",
        emailinput: "Correo Electronico",
        estudiante: "ESTUDIANTE",
        profesor: "PROFESOR",
        tecnico: "TÉCNICO",
        dashboard: "Dashboard",
        equipos: "Equipos",
        historial: "Mi historial",
        incidencias: "Incidencias/Solicitado",
        cerrarsesion: "Cerrar Sesión",
        userNormal: "Usuario",
        
        //Pagina de formularioTicket.html
        newincidencia: "Nueva Incidencia",
        detalles: "Detalles del problema",
        ayuda: "¿Que necesitas?",
        option1: "Reportar una Incidencia",
        option2: "Hacer una Solicitud",
        equipafe: "Equipo afectado",
        description: "Descripción detallada:",
        enviarticket: "Enviar Ticket",
        descriptionPlaceholder: "Describe el problema",

        //Parte de misIncidencias.html
        history: "Historial de Incidencias",
        toincidencia: "Crear Nueva Incidencia",
        tipo: "Tipo",
        estado: "Estado",
        fecha: "Fecha Creación",
        accion: "Acción",
        consultar: "consultar",

        //Parte de verTicket.html
        detalleTicket: "Detalle del Ticekt",
        backlist: "Volver al Listado",
        ticketinfo: "Detalle del Ticket Nº",
        solicitudinfo: "Detalles de la Solicitud",
        creadoinfo: "Creado por",
        equipoinfo: "Equipo",
        ubicacion: "Ubicación",
        estadoinfo: "Estado:",
        descripcioninfo: "Descripción",

        //Parte de misEquipos.html
        miequipo: "Mis Equipos",
        tuequipo: "Equipos que tienes asignados bajo tu responsabilidad:",
        buscaequipo: "Buscar equipo",
        searchbar: "Nombre, tipo, ubicación...",
        imagenEquipo: "Imagen Equipo",
        nequipo: "Nº Equipo:",

        //Parte de dashboard.html
        actividad: "Tu actividad en ClicArte",
        dashbienvenido: "Bienvenido ",
        dashordenador: "Mi ordenador asignado",
        dashequipo: "Equipo ",
        dashestado: "Planta # Aula # Estado #",
        dashincidencia: "Mis Incidencias",
        dashestadoinc: "Estado de tus incidencias y solicitudes.",
        dashhistory: "Ver Mi Historial",
        dashcrear: "Crear Nueva",
        dashestadocentro: "Estado General del Centro",
        dashoperativos: "Equipos operativos: ",
        dashequipoinc: "Equipos con incidencias"


    },

    zh: {
        logintitle: "登录",
        email: "电子邮箱",
        password: "密码",
        passwordinput: "请输入密码",
        emailinput: "请输入邮箱",
        estudiante: "学生",
        profesor: "教师",
        tecnico: "技术人员",
        dashboard: "控制面板",
        equipos: "我的设备",
        historial: "我的历史记录",
        incidencias: "故障/申请",
        cerrarsesion: "退出登录",
        userNormal: "用户界面",

        newincidencia: "申报新故障",
        detalles: "故障详细信息",
        ayuda: "你需要什么?",
        option1: "报告新故障",
        option2: "提交申请",
        equipafe: "受影响的设备",
        description: "详细备注:",
        enviarticket: "发送申请",
        descriptionPlaceholder: "请填写备注(请输入西班牙语)",
        
        history: "故障历史记录",
        toincidencia: "申报新故障",
        tipo: "设备种类",
        estado: "设备状态",
        fecha: "申报时间",
        accion: "操作",
        consultar: "详细信息",

        detalleTicket: "申报详细信息",
        backlist: "返回历史记录列表",
        ticketinfo: "申报Nº",
        solicitudinfo: "详细信息",
        creadoinfo: "申报者",
        equipoinfo: "设备",
        ubicacion: "地址",
        estadoinfo: "状态:",
        descripcioninfo: "备注",

        miequipo: "我的设备",
        tuequipo: "你所拥有的设备",
        buscaequipo: "搜索设备",
        searchbar: "名字，地址，种类(输入西班牙语)",
        imagenEquipo: "设备图片",
        nequipo: "设备编号:",

        actividad: "你在ClicArte上的活动",
        dashbienvenido: "欢迎回来 ",
        dashordenador: "我的电脑",
        dashequipo: "我的设备 ",
        dashestado: "楼 # 教室 # 状态 #",
        dashincidencia: "我的故障",
        dashestadoinc: "故障和申请状态。",
        dashhistory: "查看我的历史",
        dashcrear: "创建新的故障申报    ",
        dashestadocentro: "中心设备状态",
        dashoperativos: "正常设备: ",
        dashequipoinc: "故障设备: "
    },

    en: {
        logintitle: "Log In",
        email: "Email Address",
        password: "Password",
        passwordinput: "Enter your password",
        emailinput: "Enter your email",
        estudiante: "Student",
        profesor: "Teacher",
        tecnico: "Technician",
        dashboard: "Dashboard",
        equipos: "My Equipment",
        historial: "My History",
        incidencias: "Incidents/Requests",
        cerrarsesion: "Log Out",
        userNormal: "User",

        newincidencia: "Report a New Issue",
        detalles: "Issue Details",
        ayuda: "What do you need?",
        option1: "Report an Issue",
        option2: "Submit a Request",
        equipafe: "Affected Equipment",
        description: "Detailed Description:",
        enviarticket: "Submit Ticket",
        descriptionPlaceholder: "Describe the problem...",

        history: "Incident History",
        toincidencia: "Report a New Issue",
        tipo: "Equipment Type",
        estado: "Equipment Status",
        fecha: "Report Date",
        accion: "Action",
        consultar: "View Details",

        detalleTicket: "Incident Details",
        backlist: "Back to Incident History",
        ticketinfo: "Incident No.",
        solicitudinfo: "Details",
        creadoinfo: "Reported By",
        equipoinfo: "Equipment",
        ubicacion: "Location",
        estadoinfo: "Status:",
        descripcioninfo: "Description",

        miequipo: "My Equipment",
        tuequipo: "Equipment assigned to your responsibility",
        buscaequipo: "Search Equipment",
        searchbar: "Name, location, type (enter in Spanish)",
        imagenEquipo: "Equipment Image",
        nequipo: "Equipment Number:",

        actividad: "Your Activity on ClicArte",
        dashbienvenido: "Welcome back ",
        dashordenador: "My Computer",
        dashequipo: "My Equipment",
        dashestado: "Floor # Room # Status #",
        dashincidencia: "My Issues",
        dashestadoinc: "Issues and requests status.",
        dashhistory: "View My History",
        dashcrear: "Create a New Issue Report",
        dashestadocentro: "Center Equipment Status",
        dashoperativos: "Operational Equipment: ",
        dashequipoinc: "Faulty Equipment: "
    }
};

const fontAwesome = document.createElement('link');
fontAwesome.rel = 'stylesheet';
fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';

document.head.appendChild(fontAwesome);
//Crear un funcion para que se puede traductor la pagina
function cambiaIdioma(idioma){
    //Buscar todos etiquetas que tiene data-i18n, y coger texto de las etiquetas guardar como un array.
    const elementoIds = document.querySelectorAll("[data-i18n]");

    //Hace un foreach a todos datai18n que esta en array elementoIds
    elementoIds.forEach(function(elementoId){
        //Coger los texto de etiquetas y guardar a variable clave
        const clave = elementoId.dataset.i18n;
        //Cambiar texto a idioma que has elegido(idioma viene de boton)
        elementoId.textContent = traducciones[idioma][clave];
    });

    const placeholders = document.querySelectorAll("[data-i18n-placeholder]");
    //Este parte es lo mismo, pero es para placeholder
    placeholders.forEach(function(placeholder){
        const clave = placeholder.dataset.i18nPlaceholder;
        placeholder.placeholder = traducciones[idioma][clave];
    }
    );
}


    //Hace un envento de boton, cuando click un boton, se envia dato que guarda en este boton, como (zh, en, es)
    const botonesIdioma = document.querySelectorAll("[data-lang]")

    //Un funciona que va a mira el boton, cuando te click este boton, va a ejecuta el funcion que guarda en este boton (cambiaIdioma).
    botonesIdioma.forEach(function(boton){
        boton.addEventListener("click", function(){
            const idioma = boton.dataset.lang;
            cambiaIdioma(idioma);
    });
});

// Cambiar entre tema claro y oscuro
const botonTema = document.getElementById("theme-toggle");

if (botonTema) {
    botonTema.addEventListener("click", function () {
        const temaActual = document.documentElement.dataset.theme;

        if (temaActual === "dark") {
            document.documentElement.removeAttribute("data-theme");
            // Muestra icono de luna para cambiar a modo oscuro
            botonTema.innerHTML = '<i class="fa-solid fa-moon"></i>';
        } else {
            document.documentElement.dataset.theme = "dark";
            // Muestra icono de sol para cambiar a modo claro
            botonTema.innerHTML = '<i class="fa-solid fa-sun"></i>';
        }
    });
}
