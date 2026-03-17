@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>环信 SDK 测试</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>测试功能</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="testToken()">
                                <i class="fas fa-key"></i> 测试获取 Token
                            </button>
                            <button type="button" class="btn btn-success" onclick="testRegister()">
                                <i class="fas fa-user-plus"></i> 测试注册用户
                            </button>
                            <button type="button" class="btn btn-info" onclick="testSendMessage()">
                                <i class="fas fa-comment"></i> 测试发送消息
                            </button>
                            <button type="button" class="btn btn-warning" onclick="testGetUserInfo()">
                                <i class="fas fa-user"></i> 测试获取用户信息
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="syncUsers()">
                                <i class="fas fa-sync"></i> 同步所有用户
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>测试参数</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="fromUser" class="form-label">发送者</label>
                                <input type="text" class="form-control" id="fromUser" value="user_5" placeholder="发送者用户名">
                            </div>
                            <div class="col-md-4">
                                <label for="toUser" class="form-label">接收者</label>
                                <input type="text" class="form-control" id="toUser" value="user_3" placeholder="接收者用户名">
                            </div>
                            <div class="col-md-4">
                                <label for="message" class="form-label">消息内容</label>
                                <input type="text" class="form-control" id="message" value="你好，这是一条测试消息！" placeholder="消息内容">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="queryUsername" class="form-label">查询用户名</label>
                                <input type="text" class="form-control" id="queryUsername" value="user_3" placeholder="要查询的用户名">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5>测试结果</h5>
                        <pre id="result" class="bg-light p-3 border rounded" style="height: 400px; overflow-y: auto;">点击按钮进行测试...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function displayResult(data) {
    const resultElement = document.getElementById('result');
    resultElement.textContent = JSON.stringify(data, null, 2);
}

async function testToken() {
    try {
        const response = await fetch('/easemob/test-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        displayResult(data);
    } catch (error) {
        displayResult({ success: false, message: error.message });
    }
}

async function testRegister() {
    try {
        const response = await fetch('/easemob/test-register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        displayResult(data);
    } catch (error) {
        displayResult({ success: false, message: error.message });
    }
}

async function testSendMessage() {
    const from = document.getElementById('fromUser').value;
    const to = document.getElementById('toUser').value;
    const message = document.getElementById('message').value;

    try {
        const response = await fetch('/easemob/test-send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ from, to, message })
        });
        const data = await response.json();
        displayResult(data);
    } catch (error) {
        displayResult({ success: false, message: error.message });
    }
}

async function testGetUserInfo() {
    const username = document.getElementById('queryUsername').value;

    try {
        const response = await fetch('/easemob/test-user-info', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ username })
        });
        const data = await response.json();
        displayResult(data);
    } catch (error) {
        displayResult({ success: false, message: error.message });
    }
}

async function syncUsers() {
    try {
        const response = await fetch('/easemob/sync-users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        displayResult(data);
    } catch (error) {
        displayResult({ success: false, message: error.message });
    }
}
</script>
@endsection
