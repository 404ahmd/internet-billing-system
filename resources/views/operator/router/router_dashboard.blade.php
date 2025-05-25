@extends('operator.master')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">

                <h1>MikroTik Monitoring</h1>

                <!-- Form Tambah Router -->
                <div class="card mb-4">
                    <div class="card-header">Add New Router</div>
                    <div class="card-body">
                        <form @submit.prevent="connectRouter">

                            <button type="submit" class="btn btn-primary">Connect</button>
                        </form>
                    </div>
                </div>

                <!-- Daftar Router -->
                <div class="card">
                    <div class="card-header">Router List</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>IP</th>
                                    <th>Status</th>
                                    <th>CPU Load</th>
                                    <th>Last Seen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="router in routers" :key="router.id">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

 
@endsection
