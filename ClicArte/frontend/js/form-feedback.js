// Validación accesible de formularios marcados con data-validar.
// - Si falta un dato: muestra un resumen de errores (role="alert"), marca los
//   campos con aria-invalid y lleva el foco al primer campo incorrecto.
// - Si todo es correcto: muestra una confirmación (role="status").
//   En esta versión estática los formularios no se envían a ningún servidor.
(function () {
    'use strict';

    function textoEtiqueta(campo) {
        const label = campo.id ? document.querySelector(`label[for="${campo.id}"]`) : null;
        if (!label) return campo.name || 'Campo';
        const principal = label.querySelector('[data-i18n]') || label;
        return principal.textContent.replace(/[:*]/g, '').trim();
    }

    // Mensajes en español, independientes del idioma del navegador.
    function mensajeError(campo) {
        const v = campo.validity;
        if (v.valueMissing) {
            return campo.tagName === 'SELECT' ? 'elige una opción de la lista' : 'este campo es obligatorio';
        }
        if (v.typeMismatch && campo.type === 'email') return 'escribe un correo con el formato nombre@dominio.com';
        return 'revisa el valor introducido';
    }

    function crearCaja(form) {
        let caja = form.querySelector('.mensaje');
        if (!caja) {
            caja = document.createElement('div');
            caja.tabIndex = -1;
            form.prepend(caja);
        }
        return caja;
    }

    document.querySelectorAll('form[data-validar]').forEach(function (form) {
        form.noValidate = true; // mensajes propios en lugar de las burbujas del navegador

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const caja = crearCaja(form);
            const campos = Array.from(form.elements).filter(el => el.willValidate);
            const invalidos = campos.filter(el => !el.checkValidity());

            campos.forEach(function (el) {
                if (invalidos.includes(el)) {
                    el.setAttribute('aria-invalid', 'true');
                } else {
                    el.removeAttribute('aria-invalid');
                }
            });

            if (invalidos.length > 0) {
                caja.className = 'mensaje error';
                caja.setAttribute('role', 'alert');
                caja.innerHTML = '<p>Revisa estos campos antes de enviar:</p><ul></ul>';
                const lista = caja.querySelector('ul');
                invalidos.forEach(function (el) {
                    const li = document.createElement('li');
                    const enlace = document.createElement('a');
                    enlace.href = `#${el.id}`;
                    enlace.textContent = `${textoEtiqueta(el)}: ${mensajeError(el)}`;
                    enlace.addEventListener('click', function (ev) {
                        ev.preventDefault();
                        el.focus();
                    });
                    li.append(enlace);
                    lista.append(li);
                });
                invalidos[0].focus();
                return;
            }

            caja.className = 'mensaje success';
            caja.setAttribute('role', 'status');
            caja.textContent = 'Datos correctos. En esta versión de demostración no se envían al servidor.';
            caja.focus();
        });

        // Al corregir un campo se quita su marca de error.
        form.addEventListener('input', function (e) {
            if (e.target.checkValidity && e.target.checkValidity()) {
                e.target.removeAttribute('aria-invalid');
            }
        });
    });
})();
