@php($isEdit = $entity && $entity->exists)
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Name *</label>
            <input type="text" name="name" class="form-control form-control-sm"
                value="{{ old('name', $entity->name) }}" required maxlength="50">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Descrição *</label>
            <input type="text" name="descricao" class="form-control form-control-sm"
                value="{{ old('description', $entity->description) }}" required maxlength="255">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Localização *</label>
            <input type="text" name="location" class="form-control form-control-sm"
                value="{{ old('location', $entity->location) }}" required maxlength="255">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Site Online *</label>
            <input type="text" name="website" class="form-control form-control-sm"
                value="{{ old('website', $entity->website) }}" required maxlength="255">
        </div>
    </div>
    
</div>
<div class="d-flex" style="gap:.5rem;">
    <button type="submit" class="btn btn-save btn-sm">Guardar</button>
    <a href="{{ route('admin.entities.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
</div>






