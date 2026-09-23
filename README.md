# ClicArte
# Reconstrucción del Frontend

## Descripción

Este proyecto tiene como objetivo reorganizar y mejorar la estructura del proyecto existente, separando claramente las responsabilidades del **frontend** y del **backend**.

El proceso de reconstrucción se realiza de forma progresiva, manteniendo el funcionamiento actual del proyecto mientras se mejora la organización, la estructura HTML y la reutilización de componentes.

---

## Plan de trabajo

### 1. Separación inicial entre Frontend y Backend (Finished)

Como primer paso, se realiza una revisión general del proyecto para identificar y separar, en la medida de lo posible, las partes correspondientes al **frontend** y al **backend**.

El objetivo es conseguir una estructura más clara y facilitar el trabajo posterior sobre cada parte del proyecto.

---

### 2. Revisión del Backend (Finished)

Una vez realizada la separación inicial, se revisa el backend para localizar posibles elementos de presentación que no deberían encontrarse directamente dentro de esta parte, como código **HTML o CSS mezclado con la lógica PHP**.

En esta primera fase no se modifica directamente la lógica de los archivos PHP.

Cuando una página necesita ser trasladada o reorganizada, se mantiene temporalmente la navegación entre las páginas mediante un `header`, permitiendo que el proyecto continúe funcionando mientras se realiza la reconstrucción progresiva.

---

### 3. Revisión del Frontend

Después de revisar el backend, se comienza a trabajar principalmente sobre la parte frontend.

Primero se realiza una revisión de los archivos existentes para localizar pequeños errores o problemas de estructura HTML, por ejemplo:

* Uso innecesario o incorrecto de elementos `<div>`.
* Estructuras HTML poco claras.
* Elementos que pueden organizarse de una forma más semántica.
* Código repetido.

El objetivo es conseguir una estructura HTML más clara, legible y fácil de mantener.

---

### 4. Organización de la navegación

Una de las primeras tareas dentro del frontend consiste en revisar los diferentes archivos para localizar las barras de navegación (`nav`) que aparecen repetidas.

En lugar de mantener diferentes copias del mismo código, se pretende centralizar la navegación en un único componente reutilizable.

De esta forma, las diferentes páginas pueden utilizar el mismo componente y evitar la duplicación de código.

Esto también facilita futuras modificaciones, ya que un cambio realizado en el componente de navegación puede aplicarse a las diferentes páginas que lo utilizan.

---

### 5. Reconstrucción de la estructura HTML

También se revisan las páginas que no disponen de una estructura HTML suficientemente clara.

Cuando es necesario, se incorporan elementos semánticos como:

* `<header>`
* `<nav>`
* `<main>`
* `<section>`
* `<footer>`

El objetivo es conseguir una estructura más visual, ordenada y comprensible tanto para los usuarios como para otros desarrolladores.

---

## Objetivos de la reconstrucción

Con este proceso se pretende conseguir:

* Una separación más clara entre frontend y backend.
* Una estructura HTML semántica.
* Una mejor organización de los archivos.
* La reducción de código duplicado.
* Componentes reutilizables.
* Un código más fácil de mantener y ampliar.
* Una base preparada para implementar y mejorar el diseño responsive.
* Mantener el proyecto funcional durante el proceso de reconstrucción.

---

## Git y documentación

El desarrollo se realiza de forma progresiva utilizando **Git**, registrando los diferentes cambios realizados durante la reconstrucción.

La documentación también evoluciona junto con el proyecto, reflejando los principales cambios y decisiones tomadas durante el proceso.

De esta manera, tanto el código como la documentación avanzan de forma paralela durante el desarrollo.
