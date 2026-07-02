<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Nome da Escola *</label>
            <input type="text" name="school_name" class="form-control form-control-sm"
                value="{{ old('school_name', $protocol->school_name) }}" required maxlength="255"
                placeholder="Ex: Escola Coelho Castro">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Link (URL Externa)</label>
            <input type="url" name="link" class="form-control form-control-sm"
                value="{{ old('link', $protocol->link) }}" maxlength="500"
                placeholder="https://...">
            <small class="text-muted">Link para o protocolo ou site da escola</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Estado</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="ativo" value="1" 
                       id="ativo" {{ old('ativo', $protocol->ativo) ? 'checked' : '' }}>
                <label class="form-check-label" for="ativo">
                    Protocolo ativo
                </label>
            </div>
        </div>
    </div>
</div>
<div class="d-flex" style="gap:.5rem;">
    <button type="submit" class="btn btn-save btn-sm">Guardar</button>
    <a href="{{ route('admin.protocols.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
</div>
