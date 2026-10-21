<x-mail::message>
# ¡Bienvenido al Escape Room DAWSCAPE! 🎮

Hola **{{ $nombreEquipo }}**,

Tu equipo ha sido registrado exitosamente. Aquí está tu **código de acceso único** para comenzar el juego:

<x-mail::panel>
# {{ $codigoAcceso }}
</x-mail::panel>

## Instrucciones:
1. Guarda este código en un lugar seguro
2. El día del evento, tendrás que ingresarlo junto con tu nombre de equipo
3. **No compartas este código** con otros equipos
4. Si pierdes este código, contacta con tu profesor/coordinador

## Detalles de tu inscripción:
- **Equipo:** {{ $nombreEquipo }}
- **Clase:** {{ $inscripcion->clase }}
- **Correo:** {{ $inscripcion->correo }}

---

Si tienes cualquier problema, contacta con el coordinador del evento.

**¡Nos vemos en el Escape Room! 🔓**

@component('mail::footer')
DAWSCAPE - Escape Room Digital | {{ config('app.name') }}
@endcomponent
</x-mail::message>
