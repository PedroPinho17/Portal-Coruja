@php($isEdit = $formation && $formation->exists)
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Nome *</label>
            <input type="text" name="name" class="form-control form-control-sm"
                value="{{ old('name', $formation->name) }}" required maxlength="50">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Descrição *</label>
            <input type="text" name="description" class="form-control form-control-sm"
                value="{{ old('description', $formation->description) }}" required maxlength="255">
        </div>
    </div>  

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Duração *</label>
            <input type="text" name="duration" class="form-control form-control-sm"
                value="{{ old('duration', $formation->duration) }}" required maxlength="255">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Localização *</label>
            <input type="text" name="location" class="form-control form-control-sm"
                value="{{ old('location', $formation->location) }}" required maxlength="9">
        </div>
    </div> 
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Entidade *</label>
            <select name="id_entity" class="form-select form-select-sm" required>
                <option value="">Selecione uma entidade</option>
                @foreach($entities as $entity)
                    <option value="{{ $entity->id }}" {{ old('id_entity', $formation->id_entity) == $entity->id ? 'selected' : '' }}>
                        {{ $entity->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    @if(!$isEdit)
        <div class="col-md-4">
            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" name="active" value="0">       
                    <input class="form-check-input" type="checkbox" value="1" id="active" name="active" {{ old('active', $formation->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Ativo</label>
                </div>
            </div>
        </div> 
    @endif
</div>
<div class="d-flex" style="gap:.5rem;">
    <button type="submit" class="btn btn-save btn-sm">Guardar</button>
    <a href="{{ route('admin.formations.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
</div>






