<style>
    .textarea {
        min-height: 99px;
        max-height: 100px;
    }
</style>
@extends('layout.app')
@section('content')
    <div class="grid grid-cols-1 gap-4 text-center">
        <h1 class="text-2xl font-bold">Contacto</h1>
    </div>
    <div class="container mx-auto grid grid-cols-1 gap-4">

        @livewire('contact')
        {{-- <div class=" mt-4 mb-4">
            <h3 class="titleSection">{{ $result['description'] }}</h3>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div class="column col-md-5">
                <input type="text" class="form-control col-12 bg-light mb-3" placeholder="Nombre">
                <input type="tel" max="10" class="form-control col-12 bg-light mb-3" placeholder="Teléfono">
                <input type="email" class="form-control col-12 bg-light mb-3" placeholder="Correo">
                <textarea class="form-control bg-light mb-3 textarea" placeholder="Mensaje"></textarea>
            </div> --}}
        {{-- <div class="column col-md-5">
                <label for="" class="col-12 mb-3"><img src="{{ asset('icons/ubicacion.png') }}"
                        alt="">&nbsp;&nbsp; Horticultura 271 Colonia 20 de Noviembre C.P. 15300 Delegación
                    Venustiano Carranza</label>
                <label for="" class="col-12 mb-3"><img src="{{ asset('icons/llamada-telefonica.png') }}"
                        alt="">&nbsp;&nbsp; (52-55) 55-59-20-17</label>
                <label for="" class="col-12 mb-3"><img src="{{ asset('icons/email.png') }}"
                        alt="">&nbsp;&nbsp; Correo</label>
                <label for="" class="col-12 mb-3"><img src="{{ asset('icons/whatsapp.png') }}"
                        alt="">&nbsp;&nbsp; WhatsApp</label>
            </div> --}}
    </div>
    </div>
@endsection
