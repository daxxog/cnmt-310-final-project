<svelte:head>
    <title>Quiz App: Login</title>
</svelte:head>


<script>
    import { goto } from '$app/navigation';

    import { BACKEND_ENDPOINT, CREDENTIAL_MODE } from '../lib/const.js';
    import { ErrorHandler } from '../lib/error_handler.js';


    let loginErrorHandler = new ErrorHandler();

    class LoginValues {
        resetErrorHandler() {
            loginErrorHandler = new ErrorHandler();
        }

        set username(_username) {
            this.resetErrorHandler();
            this._username = _username;
        }

        get username() {
            return this._username;
        }

        set password(_password) {
            this.resetErrorHandler();
            this._password = _password;
        }

        get password() {
            return this._password;
        }
    }

    const loginValues = new LoginValues();
    let loggingIn = false;

    const handleSubmit = async e => {
        loggingIn = true;

        const formData = new FormData();
        formData.append('username', loginValues.username);
        formData.append('password', loginValues.password);

        const response = await fetch(BACKEND_ENDPOINT + '/auth.php', {
            credentials: CREDENTIAL_MODE,
            method: 'POST',
            body: formData
        }).then( res => res.json() );

        loggingIn = false;

        if( (response.success ?? false) === true) {
            await goto('./');
        } else {
            const errors = new ErrorHandler();
            errors.addError('Invalid username or password!');
            loginErrorHandler = errors;
        }
    };
</script>


<style>
div.error {
    color: red;
}
span.error-header {
    font-size: 120%;
    font-weight: bold;
}
button.bad-button {
    background-color: red;
}
</style>


<div class="row">
    <div class="twelve columns">
        <br>
    </div>
</div>
<div class="row">
    <div class="three columns">
        &nbsp;
    </div>
    <div class="six columns">
        <h1>Quiz App Login</h1>
    </div>
    <div class="three columns">
        &nbsp;
    </div>
</div>
<div class="row">
    <div class="three columns">
        &nbsp;
    </div>
    <div class="six columns">
        {#if loginErrorHandler.hasError}
            <div class="error">
                <span class="error-header">There were some problems with your login.</span>
                <br><br>
                <ul>
                    {#each loginErrorHandler.errors as error}
                        <li>{error}</li>
                    {/each}
                </ul>
            </div>
        {/if}
    </div>
    <div class="three columns">
        &nbsp;
    </div>
</div>
<form on:submit|preventDefault={handleSubmit}>
    <div class="row">
        <div class="three columns">
            &nbsp;
        </div>
        <div class="six columns">
            <label for="username">Username:</label><br>
            <input class="u-full-width" type="text" id="username" name="username" bind:value="{loginValues.username}"><br>
        </div>
        <div class="three columns">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="three columns">
            &nbsp;
        </div>
        <div class="six columns">
            <label for="password">Password:</label><br>
            <input class="u-full-width" type="password" id="password" name="password" bind:value="{loginValues.password}"><br>
        </div>
        <div class="three columns">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="three columns">
            &nbsp;
        </div>
        <div class="six columns">
            <div style="text-align: right">
                <button disabled="{loggingIn}" class:button-primary="{!loggingIn}" class:bad-button="{loginErrorHandler.hasError}" type="submit" value="login">
                    {#if loginErrorHandler.hasError}
                        Error
                    {:else}
                        {#if loggingIn}
                            Logging in
                        {:else}
                            Login
                        {/if}
                    {/if}
                </button> <br>
            </div>
        </div>
        <div class="three columns">
            &nbsp;
        </div>
    </div>
</form>
