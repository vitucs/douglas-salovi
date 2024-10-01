<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS for Styling -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: #fff;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .main-content {
            padding: 20px;
        }
        .nav-item {
            margin-bottom: 10px;
        }
        .card {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>

    <script>
        function updateValue() {
            var select = document.getElementById("course");
            var valueInput = document.getElementById("value");
            var selectedValue = select.options[select.selectedIndex].dataset.value;
            valueInput.value = selectedValue;
        }
    </script>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center">Meu Dashboard</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#perfil" data-bs-toggle="tab">
                    <i class="fas fa-user-circle"></i> Meu Perfil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#nova-compra" data-bs-toggle="tab">
                    <i class="fas fa-plus-circle"></i> Criar Pedido
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#historico-compras" data-bs-toggle="tab">
                    <i class="fas fa-shopping-cart"></i> Histórico de Pedidos
                </a>
            </li>
            <li class="nav-item">
                @if(session('logged_in_user.role') !== 'Vendedor')
                    <a class="nav-link" href="#equipe" data-bs-toggle="tab">
                        <i class="fas fa-shopping-cart"></i> Equipe
                    </a>
                @endif
            </li>

        </ul>
        <!-- Logout button -->
        <div class="mt-4">
            <form method="POST" action="{{ route('reset.user') }}">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Logout</button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content flex-fill">
        <div class="tab-content">
            <!-- Perfil do Usuário Tab -->
            <div class="tab-pane fade show active" id="perfil">
                <div class="container">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Perfil do Usuário</h4>
                            <hr>
                            <form method="POST" action="{{ route('update.user') }}">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ session('logged_in_user.name') }}" placeholder="Nome do Usuário">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ session('logged_in_user.email') }}" placeholder="email@exemplo.com" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ session('logged_in_user.phone') }}" placeholder="(99) 99999-9999">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="address" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ session('logged_in_user.address') }}" placeholder="Rua, Cidade, País">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="role" class="form-label">Cargo</label>
                                        <select id="role" name="role" class="form-select" {{ session('logged_in_user.role') !== 'Gestor' ? 'disabled' : '' }}>
                                            <option value="" disabled {{ !session('logged_in_user.role') ? 'selected' : '' }}>Selecione um cargo</option>
                                            <option value="Gestor" {{ session('logged_in_user.role') == 'Gestor' ? 'selected' : '' }}>Gestor</option>
                                            <option value="Supervisor" {{ session('logged_in_user.role') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                            <option value="Coordenador de Equipe" {{ session('logged_in_user.role') == 'Coordenador de Equipe' ? 'selected' : '' }}>Coordenador de Equipe</option>
                                            <option value="Vendedor" {{ session('logged_in_user.role') == 'Vendedor' ? 'selected' : '' }}>Vendedor</option>
                                            <option value="Dono da Empresa" {{ session('logged_in_user.role') == 'Dono da Empresa' ? 'selected' : '' }}>Dono da Empresa</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company" class="form-label">Empresa</label>
                                        <input type="text" class="form-control" id="company" name="company" value="{{ session('logged_in_user.company') }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Compras Tab -->
            <div class="tab-pane fade" id="historico-compras">
                <div class="container">
                    <div class="card">
                        <div class="card-body">
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Histórico de Pedidos</h4>
                            </div>
                            <hr>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Valor</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($values) && count($values) > 0)
                                        @foreach ($values as $index => $row)
                                        <tr>
                                            <td>{{ $row[2] ?? 'N/A' }}</td>
                                            <td>{{ $row[3] ?? 'N/A' }}</td>
                                            <td>{{ $row[4] ?? 'N/A' }}</td>
                                            <td>
                                            <form method="POST" action="{{ route('remove.order') }}">
                                                @csrf
                                                <input type="hidden" name="index" value="{{ $index }}">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i> <!-- Ícone de lixeira -->
                                                </button>
                                            </form>

                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">Nenhuma compra encontrada.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Criar Pedido Tab -->
            <div class="tab-pane fade" id="nova-compra">
                <div class="container">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Criar Pedido</h4>
                            <hr>
                            <form method="POST" action="{{ route('store.order') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="course" class="form-label">Método</label>
                                    <select id="course" name="course" class="form-select" onchange="updateValue()" required>
                                        <option value="" data-value="">Selecione um Método</option>
                                        <option value="Metodo_X" data-value="799.00">Método X - R$799,00</option>
                                        <option value="Metodo_Y" data-value="1399.00">Método Y - R$1399,00</option>
                                        <option value="Metodo_Z" data-value="2699.00">Método Z - R$2699,00</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="value" class="form-label">Valor</label>
                                    <input type="number" step="0.01" class="form-control" id="value" name="value" placeholder="Valor do Produto" required readonly>
                                </div>
                                <button type="submit" class="btn btn-success">Criar Pedido</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipe Tab -->
            <div class="tab-pane fade" id="equipe">
                <div class="container">
                    <div class="card">
                        <div class="card-body">
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Equipe</h4>
                            </div>
                            <hr>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo</th>
                                        <th>Empresa</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($equipe) && count($equipe) > 0)
                                        @foreach ($equipe as $index => $row)
                                        <tr>
                                            <td>{{ $row[1] ?? 'N/A' }}</td>
                                            <td>{{ $row[2] ?? 'N/A' }}</td>
                                            <td>{{ $row[7] ?? 'N/A' }}</td>
                                            <td>{{ $row[8] ?? 'N/A' }}</td>
                                            <td>
                                                @if(session('logged_in_user.email') !== $row[2])
                                                    <form method="POST" action="{{ route('remove.employer') }}">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">Nenhum funcionário vinculado à empresa.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
