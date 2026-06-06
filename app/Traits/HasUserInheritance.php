<?php

namespace App\Traits;

/**
 * Trait para modelos que heredan de la tabla `usuario` via TPT (Table Per Type).
 *
 * Permite acceder a atributos del usuario padre de forma transparente:
 *   $postulante->email       → $postulante->usuario->correo
 *   $postulante->nombre      → $postulante->usuario->nombre
 *   $postulante->fullName    → "{nombre} {apellidos}"
 *
 * Requisitos del modelo que use este trait:
 * - Debe tener una relación `usuario()` definida como belongsTo(Usuario::class, ...)
 * - La PK del modelo hijo debe ser FK a usuario.id_usuario
 */
trait HasUserInheritance
{
    /**
     * Atributos de `usuario` que se delegan automáticamente.
     * Override en el modelo hijo para personalizar.
     */
    protected function inheritedAttributes(): array
    {
        return [
            'nombre', 'apellidos', 'ci', 'fechanac', 'sexo',
            'direccion', 'telefono', 'rol', 'correo', 'fecha',
        ];
    }

    /**
     * Aliases: mapean nombres amigables a columnas de usuario.
     * Permite $postulante->email en lugar de $postulante->correo.
     */
    protected function inheritedAliases(): array
    {
        return [
            'email'     => 'correo',
            'full_name' => null, // se resuelve en getInheritedAttribute()
            'fullName'  => null,
        ];
    }

    /**
     * Intercepta acceso a atributos no encontrados en el modelo hijo
     * y delega al usuario padre si corresponde.
     */
    public function getAttribute($key): mixed
    {
        // 1. Primero intentar resolver del modelo hijo (prioridad)
        $value = parent::getAttribute($key);
        if ($value !== null || array_key_exists($key, $this->attributes)) {
            return $value;
        }

        // 2. Verificar si es un atributo heredado
        return $this->getInheritedAttribute($key);
    }

    /**
     * Resuelve un atributo desde el usuario padre.
     */
    protected function getInheritedAttribute(string $key): mixed
    {
        // Resolver aliases
        $aliases = $this->inheritedAliases();
        if (array_key_exists($key, $aliases)) {
            if ($key === 'full_name' || $key === 'fullName') {
                return $this->getFullNameAttribute();
            }
            $key = $aliases[$key];
        }

        // Verificar si es un atributo heredado
        if (in_array($key, $this->inheritedAttributes(), true)) {
            // Lazy load: cargar la relación solo si no está cargada
            if (!$this->relationLoaded('usuario')) {
                $this->load('usuario');
            }
            return $this->usuario?->{$key};
        }

        return null;
    }

    /**
     * Nombre completo: "{nombre} {apellidos}"
     */
    public function getFullNameAttribute(): string
    {
        if (!$this->relationLoaded('usuario')) {
            $this->load('usuario');
        }
        $usuario = $this->usuario;
        return $usuario ? "{$usuario->nombre} {$usuario->apellidos}" : '';
    }

    /**
     * Nombre completo (alias en español).
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Verifica si el usuario padre está cargado para evitar N+1.
     * Útil en colecciones: Postulante::with('usuario')->get()
     */
    public function ensureUserLoaded(): static
    {
        if (!$this->relationLoaded('usuario')) {
            $this->load('usuario');
        }
        return $this;
    }

    /**
     * Scope para eager-load siempre la relación usuario.
     * Uso: Postulante::withUser()->get()
     */
    public function scopeWithUser($query)
    {
        return $query->with('usuario');
    }

    /**
     * Convierte el modelo a array incluyendo atributos del usuario padre.
     * Útil para JSON responses y debugging.
     */
    public function toArrayWithUser(): array
    {
        $data = $this->toArray();

        if ($this->usuario) {
            foreach ($this->inheritedAttributes() as $attr) {
                if (!isset($data[$attr])) {
                    $data[$attr] = $this->usuario->{$attr};
                }
            }
            $data['full_name'] = $this->fullName;
        }

        return $data;
    }
}
