<x-layout-app page-title="Editar a Ficha do Cliente">

    <div class="border-light shadow w-100 p-3">
        <h4>Alterar o Cliente</h4>
        <hr>

        @if($errors->any())
            <span class="alert alert-danger" style="color: #ff0000;">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach

            </span>
            <br>
        @endif

        <form action="{{ route('cliente.update-cliente',  ['id' => Crypt::encrypt($clientes->id)]) }}" method="post">

            @csrf

            <input type="hidden"  name="id" value="{{ $clientes->id }}">

            <div class="container-fluid">
                <div class="row gap-2">
                    {{-- Editar cliente --}}
                    <div class="col" >
                        <div  class="row">
                            <div class="col-md-2 col-sm-12 mb-2">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" required maxlength="14" size=14 data-mask="000.000.000-00" value="{{ old('cpf',$clientes->cpf) }}">
                                @error('cpf')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-12 mb-2">
                                <label for="cns" class="form-label">Número do cartão do SUS (opcional)</label>
                                <input type="text" class="form-control" id="cns"
                                        name="cns" maxlength="20" size=20 data-mask="000.0000.0000.0000" value="{{ old('cns', $clientes->cns) }}">
                                @error('cns')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-sm-12 mb-2">
                                <label for="nome" class="form-label">Nome Completo do Cliente</label>
                                <input type="text" class="form-control" id="nome" name="nome" oninput="this.value = this.value.toUpperCase()" value="{{ old('nome', $clientes->nome) }}">
                                @error('nome')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-12 mb-2">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"  placeholder="YYYY-mm-dd" value="{{ old('data_nascimento', $clientes->data_nascimento) }}">
                                @error('data_nascimento')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-2 col-sm-12 mb-2">
                                        <label for="sexo" class="form-label">Sexo</label>
                                        <select name="sexo" class="form-select {{$errors->has('sexo')?'is-invalid':''}}" id="sexo">
                                            <option value=""> Selecione</option>
                                            <option {{old('sexo', $clientes->sexo) == "MASCULINO" ? 'selected': ''}} value="MASCULINO">MASCULINO</option>
                                            <option {{old('sexo', $clientes->sexo) == "FEMININO"  ? 'selected': ''}} value="FEMININO">FEMININO</option>
                                        </select>
                                        @error('sexo')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror

                                </div>

                                <div class="col-md-2 col-sm-12 mb-2">
                                    <div class="form-group">
                                        <label for="nacionalidade" class="form-label">Nacionalidade</label>
                                        <select name="nacionalidade" class="form-select {{$errors->has('nacionalidade')?'is-invalid':''}}" id="nacionalidade" >
                                            <option value=""> Selecione</option>
                                            <option {{old('nacionalidade', $clientes->nacionalidade) == "BRASILEIRA"  ? 'selected': ''}} value="BRASILEIRA">BRASILEIRA</option>
                                            <option {{old('nacionalidade', $clientes->nacionalidade) == "ESTRANGEIRA" ? 'selected': ''}} value="ESTRANGEIRA">ESTRANGEIRA</option>
                                        </select>
                                        @error('nacionalidade')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12 mb-2">
                                    <label for="cor" class="form-label">Cor</label>
                                    <select name="cor" class="form-select {{$errors->has('cor')?'is-invalid':''}}" id="cor" >
                                        <option value=""> Selecione</option>
                                        <option {{old('cor', $clientes->cor) == "BRANCA"   ? 'selected': ''}} value="BRANCA">  BRANCA</option>
                                        <option {{old('cor', $clientes->cor) == "PRETA"    ? 'selected': ''}} value="PRETA">   PRETA</option>
                                        <option {{old('cor', $clientes->cor) == "AMARELA"  ? 'selected': ''}} value="AMARELA"> AMARELA</option>
                                        <option {{old('cor', $clientes->cor) == "PARDA"    ? 'selected': ''}} value="PARDA">   PARDA</option>
                                        <option {{old('cor', $clientes->cor) == "INDIGINA" ? 'selected': ''}} value="INDIGINA">INDÍGENA</option>
                                    </select>
                                    @error('cor')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="etnia" class="form-label">Etnia</label>
                                    <input type="text" class="form-control" id="etnia" name="etnia" oninput="this.value = this.value.toUpperCase()" value="{{ old('etnia', $clientes->etnia) }}">
                                    @error('etnia')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            <div class="col-md-2 col-sm-12 mb-2">
                                <div class="form-group">
                                    <label for="estado_civil" class="form-label">Estado Civil</label>
                                    <select  name="estado_civil" class="form-select {{$errors->has('estado_civil')?'is-invalid':''}}" id="estado_civil">
                                        <option value=""> Selecione</option>
                                        <option {{old('estado_civil', $clientes->estado_civil) == "SOLTEIRO(A)"   ? 'selected': ''}} value="SOLTEIRO(A)">SOLTEIRO(A)</option>
                                        <option {{old('estado_civil', $clientes->estado_civil) == "CASADO(A)"     ? 'selected': ''}} value="CASADO(A)">CASADO(A)</option>
                                        <option {{old('estado_civil', $clientes->estado_civil) == "VIÚVO(A)"      ? 'selected': ''}} value="VIÚVO(A)">VIÚVO(A)</option>
                                        <option {{old('estado_civil', $clientes->estado_civil) == "DIVORCIADO(A)" ? 'selected': ''}} value="DIVORCIADO(A)">DIVORCIADO(A)</option>
                                        <option {{old('estado_civil', $clientes->estado_civil) == "UNIÃO ESTÁVEL" ? 'selected': ''}} value="UNIÃO ESTÁVEL">UNIÃO ESTÁVEL</option>
                                    </select>
                                    @error('estado_civil')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <label for="cep" class="form-label">Cep</label>
                                <input type="text" class="form-control" id="cep" name="cep" maxlength="9" data-mask="00000-000"  size=9 value="{{ old('cep', $clientes->cep) }}">
                                @error('cep')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <div class="form-group">
                                    <label for="logradouro" class="form-label">Logradouro</label>
                                    <select  name="logradouro" class="form-select {{$errors->has('logradouro')?'is-invalid':''}}" id="logradouro">
                                        <option value=""> Selecione</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "RUA"         ? 'selected': ''}} value="RUA">RUA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "AVENIDA"     ? 'selected': ''}} value="AVENIDA">AVENIDA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "TRAVESSA"    ? 'selected': ''}} value="TRAVESSA">TRAVESSA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "VIELA"       ? 'selected': ''}} value="VIELA">VIELA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "TRECHO"      ? 'selected': ''}} value="TRECHO">TRECHO</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "ALAMEDA"     ? 'selected': ''}} value="ALAMEDA">ALAMEDA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "CHÁCARA"     ? 'selected': ''}} value="CHÁCARA">CHÁCARA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "FAZENDA"     ? 'selected': ''}} value="FAZENDA">FAZENDA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "LOTEAMENTO"  ? 'selected': ''}} value="LOTEAMENTO">LOTEAMENTO</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "SITIO"       ? 'selected': ''}} value="SITIO">SITIO</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "COLONIA"     ? 'selected': ''}} value="COLONIA">COLONIA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "CONDOMINIO"  ? 'selected': ''}} value="CONDOMINIO">CONDOMINIO</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "ESTRADA"     ? 'selected': ''}} value="ESTRADA">ESTRADA</option>
                                        <option {{old('logradouro', $clientes->logradouro) == "BECO"        ? 'selected': ''}} value="BECO">BECO</option>
                                    </select>
                                    @error('logradouro')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-7 col-sm-12">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="endereco" name="endereco" oninput="this.value = this.value.toUpperCase()" value="{{ old('endereco', $clientes->endereco) }}">
                                @error('endereco')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-1 col-sm-12">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="{{ old('numero', $clientes->numero) }}">
                                @error('numero')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-12 m">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="bairro" name="bairro" oninput="this.value = this.value.toUpperCase()" value="{{ old('bairro', $clientes->bairro) }}">
                                @error('bairro')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" oninput="this.value = this.value.toUpperCase()" default-value="CUIABÁ" value="{{ old('cidade', $clientes->cidade) }}">
                                @error('cidade')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label for="uf" class="form-label">Estado</label>
                                    <select name="uf" class="form-select {{$errors->has('uf')?'is-invalid':''}}" id="uf">
                                        <option value=""> Selecione</option>
                                        <option {{old('uf', $clientes->uf) == "AC" ? 'selected': ''}} value="AC">ACRE</option>
                                        <option {{old('uf', $clientes->uf) == "AL" ? 'selected': ''}} value="AL">ALAGOAS</option>
                                        <option {{old('uf', $clientes->uf) == "AP" ? 'selected': ''}} value="AP">AMAPÁ</option>
                                        <option {{old('uf', $clientes->uf) == "AM" ? 'selected': ''}} value="AM">AMAZONAS</option>
                                        <option {{old('uf', $clientes->uf) == "BA" ? 'selected': ''}} value="BA">BAHIA</option>
                                        <option {{old('uf', $clientes->uf) == "CE" ? 'selected': ''}} value="CE">CEARÁ</option>
                                        <option {{old('uf', $clientes->uf) == "DF" ? 'selected': ''}} value="DF">DISTRITO FEDERAL</option>
                                        <option {{old('uf', $clientes->uf) == "ES" ? 'selected': ''}} value="ES">ESPÍRITO SANTO</option>
                                        <option {{old('uf', $clientes->uf) == "GO" ? 'selected': ''}} value="GO">GOIÁS</option>
                                        <option {{old('uf', $clientes->uf) == "MA" ? 'selected': ''}} value="MA">MARANHÃO</option>
                                        <option {{old('uf', $clientes->uf) == "MT" ? 'selected': ''}} value="MT">MATO GROSSO</option>
                                        <option {{old('uf', $clientes->uf) == "MS" ? 'selected': ''}} value="MS">MATO GROSSO DO SUL</option>
                                        <option {{old('uf', $clientes->uf) == "MG" ? 'selected': ''}} value="MG">MINAS GERAIS</option>
                                        <option {{old('uf', $clientes->uf) == "PA" ? 'selected': ''}} value="PA">PARÁ</option>
                                        <option {{old('uf', $clientes->uf) == "PB" ? 'selected': ''}} value="PB">PARAÍBA</option>
                                        <option {{old('uf', $clientes->uf) == "PR" ? 'selected': ''}} value="PR">PARANÁ</option>
                                        <option {{old('uf', $clientes->uf) == "PE" ? 'selected': ''}} value="PE">PERNAMBUCO</option>
                                        <option {{old('uf', $clientes->uf) == "PI" ? 'selected': ''}} value="PI">PIAUÍ</option>
                                        <option {{old('uf', $clientes->uf) == "RJ" ? 'selected': ''}} value="RJ">RIO DE JANEIRO</option>
                                        <option {{old('uf', $clientes->uf) == "RN" ? 'selected': ''}} value="RN">RIO GRANDE DO NORTE</option>
                                        <option {{old('uf', $clientes->uf) == "RS" ? 'selected': ''}} value="RS">RIO  GRANDE DO SUL</option>
                                        <option {{old('uf', $clientes->uf) == "RO" ? 'selected': ''}} value="RO">RONDÔNIA</option>
                                        <option {{old('uf', $clientes->uf) == "RR" ? 'selected': ''}} value="RR">RORAIMA</option>
                                        <option {{old('uf', $clientes->uf) == "SC" ? 'selected': ''}} value="SC">SANTA CATARINA</option>
                                        <option {{old('uf', $clientes->uf) == "SP" ? 'selected': ''}} value="SP">SÃO PAULO</option>
                                        <option {{old('uf', $clientes->uf) == "SE" ? 'selected': ''}} value="SE">SERGIPE</option>
                                        <option {{old('uf', $clientes->uf) == "TO" ? 'selected': ''}} value="TO">TOCANTINS</option>
                                        <option {{old('uf', $clientes->uf) == "EX" ? 'selected': ''}} value="EX">ESTRANGEIRO</option>
                                    </select>
                                    @error('uf')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2 col-sm-12 mb-2">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" data-mask="(00)00000-0000"  size=15 value="{{ old('telefone', $clientes->telefone)}}">
                                @error('telefone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8 col-sm-12 mb-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" oninput="this.value = this.value.toUpperCase()" value="{{ old('email', $clientes->email) }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-12 mb-2">
                                <label for="tipo_sangue" class="form-label">Tipo Sanguineo</label>
                                <select name="tipo_sangue" class="form-select {{$errors->has('tipo_sangue')?'is-invalid':''}}" id="tipo_sangue">
                                    <option value=""> Selecione</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "NS"  ? 'selected': ''}} value="NS">Não Sabe</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "O+"  ? 'selected': ''}} value="O+">O+</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "O-"  ? 'selected': ''}} value="O-">O-</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "A+"  ? 'selected': ''}} value="A+">A+</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "A-"  ? 'selected': ''}} value="A-">A-</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "B+"  ? 'selected': ''}} value="B+">B+</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "B-"  ? 'selected': ''}} value="B-">B-</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "AB+" ? 'selected': ''}} value="AB+">AB+</option>
                                    <option  {{old('tipo_sangue', $clientes->tipo_sangue) == "AB-" ? 'selected': ''}} value="AB-">AB-</option>
                                </select>
                                @error('tipo_sangue')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-12 mb-2">
                                <label for="ativ_laboral" class="form-label">Atividade Laboral</label>
                                <input type="text" class="form-control" id="ativ_laboral" name="ativ_laboral" oninput="this.value = this.value.toUpperCase()" value="{{ old('ativ_laboral', $clientes->ativ_laboral) }}">
                                @error('ativ_laboral')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8 col-sm-12 mb-2">
                                <label for="observacao" class="form-label">Observação</label>
                                <input type="text" class="form-control" id="observacao" name="observacao" oninput="this.value = this.value.toUpperCase()" value="{{ old('observacao' ,$clientes->observacao) }}">
                                @error('observacao')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <a href="{{ route('clientes') }}" class="btn btn-outline-danger me-3">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>

            </div>

        </form>

    </div>
    {{--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    ---}}
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#cep").val("");
                $("#endereco").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#uf").val("");
                $("#cep").focus();
                //$("#ibge").val("");
            }

            //Quando o campo cep perde o foco.
            $("#cep").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#endereco").val("...");
                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");
                        //$("#ibge").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                rua= dados.logradouro.toUpperCase();
                                bairro = dados.bairro.toUpperCase();
                                logradouro= rua.substring(0, rua.indexOf(" ") + 0).toUpperCase();
                                rua= rua.substring(rua.indexOf(" ") + 1, 60);
                                cidade = dados.localidade.toUpperCase();
                                uf = dados.uf.toUpperCase();
                                $("#logradouro").val(logradouro);
                                $("#endereco").val(rua);
                                $("#bairro").val(bairro);
                                $("#cidade").val(cidade);
                                $("#uf").val(uf);
                                $("#numero").focus();
                            } else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                Swal.fire({
                                        position: "top-center",
                                        icon: "error",
                                        title: "CEP não encontrado!",
                                        showConfirmButton: false,
                                        timer: 2500
                                });
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        Swal.fire({
                            position: "top-center",
                            icon: "error",
                            title: "CEP não encontrado!",
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
        });
    </script>
    <script>
           $(document).ready(function(){
              $('.date').mask('00/00/0000');
              $('.time').mask('00:00:00');
              $('.date_time').mask('00/00/0000 00:00:00');
              $('.cns').mask('000.0000.0000.0000');
              $('.cep').mask('00000-000');
              $('.phone').mask('0000-0000');
              $('.telefone').mask('(00)00000-0000');
              $('.phone_us').mask('(000) 000-0000');
              $('.mixed').mask('AAA 000-S0S');
              $('.cpf').mask('000.000.000-00', {reverse: true});
              $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
              $('.money').mask('000.000.000.000.000,00', {reverse: true});
              $('.money2').mask("#.##0,00", {reverse: true});
              $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                  'Z': {
                    pattern: /[0-9]/, optional: true
                  }
                }
              });
              $('.ip_address').mask('099.099.099.099');
              $('.percent').mask('##0,00%', {reverse: true});
              $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
              $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
              $('.fallback').mask("00r00r0000", {
                  translation: {
                    'r': {
                      pattern: /[\/]/,
                      fallback: '/'
                    },
                    placeholder: "__/__/____"
                  }
                });
              $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});
            });

    </script>


</x-layout-app>

