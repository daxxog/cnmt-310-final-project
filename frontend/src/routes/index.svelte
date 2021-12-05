<svelte:head>
    <title>Quiz App: Home</title>
</svelte:head>

<script>
    import { BACKEND_ENDPOINT, CREDENTIAL_MODE } from '../lib/const.js';
    import { requireLogin, gotoLogin } from '../lib/goto_login.js';
    import { ErrorHandler } from '../lib/error_handler.js';


    let quizErrorHandler = new ErrorHandler();

    const horriblyWrong = () => {
        const errors = new ErrorHandler();
        errors.addError('Something went horribly wrong!');
        errors.addError('Please contact support@example.com');
        quizErrorHandler = errors;
    };

    class Quiz {
        constructor() {
            this.didFetchQuestion = false;
        }

        set update(_update) {
            this._update = _update;
        }

        get update() {
            return this._update ?? (() => {});
        }

        // helps update the DOM related to this object in svelte
        updateDOM() {
            const update = this.update;
            return update();
        }

        set awaitingQuestion(_awaitingQuestion) {
            this._awaitingQuestion = _awaitingQuestion;
        }

        get awaitingQuestion() {
            return this._awaitingQuestion ?? true;
        }

        async fetchQuestion() {
            // try fetching a quiz question
            this.questionResponse = await fetch(BACKEND_ENDPOINT + '/q.php',
                             { credentials: CREDENTIAL_MODE }).
                             then( res => res.json() );

            // set the boolean indicating we have fetched a question
            this.didFetchQuestion = true;

            // reset the boolean indicating we are waiting for a question
            this.awaitingQuestion = false;

            // update the DOM to display our question
            this.updateDOM();
        }

        get question() {
            return this.questionResponse?.question ?? '';
        }

        get hasQuestion() {
            return this.questionResponse?.success ?? false;
        }

        set answer(_answer) {
            this._answer = _answer;
        }

        get answer() {
            return this._answer ?? '';
        }

        async checkAnswer() {
            // don't allow the user to submit another answer until we grab another question
            this.awaitingQuestion = true;

            // update the DOM so the "Check Answer" button is actually disabled
            this.updateDOM();

            // append the answer to the formdata we are going to send
            const formData = new FormData();
            formData.append('answer', this.answer);

            // clear our answer from the text box
            // (will be rendered after the fetch and answer reseults)
            this._answer = '';

            const response = await fetch(BACKEND_ENDPOINT + '/a.php', {
                credentials: CREDENTIAL_MODE,
                method: 'POST',
                body: formData
            }).then( res => res.json() );

            if( (response.success ?? false) === true) {
                // update correct and incorrect values if we need to
                this.correct = response.correct_answers ?? this.correct;
                this.incorrect = response.incorrect_answers ?? this.incorrect;

                // update the DOM to display the results
                this.updateDOM();

                // try fetching another question
                await this.fetchQuestion();
            } else {
                horriblyWrong();
            }
        }

        get hasAnswered() {
            return this._hasAnswered ?? false;
        }

        async fetchInfo() {
            // try fetching quiz info (for if a user refreshes the page)
            const response = await fetch(BACKEND_ENDPOINT + '/info.php',
                             { credentials: CREDENTIAL_MODE }).
                             then( res => res.json() );

            if( (response.success ?? false) === true) {
                this._correct = response.correct_answers ?? 0;
                this._incorrect = response.incorrect_answers ?? 0;
            } else {
                horriblyWrong();
            }

            // update the DOM to display our info
            this.updateDOM();
        }

        set correct(_correct) {
            // set the boolean indicating we have answered a question
            this._hasAnswered = true;

            // if the value we are setting is different than the value that was
            // previously set, that means that the last answer was correct.
            this._lastAnswerCorrect = this.correct !== _correct;

            this._correct = _correct;
        }

        get correct() {
            return this._correct ?? 0;
        }

        set incorrect(_incorrect) {
            this._incorrect = _incorrect;
        }

        get incorrect() {
            return this._incorrect ?? 0;
        }

        get lastAnswerCorrect() {
            return this._lastAnswerCorrect ?? false;
        }
    }

    let quiz = new Quiz();
    quiz.update = () => {
        quiz = quiz;
    };
    const checkAnswer = async e => {
        return await quiz.checkAnswer();
    };

    const doLogout = async () => {
        // call logout endpoint
        const response = await fetch(BACKEND_ENDPOINT + '/logout.php',
                         { credentials: CREDENTIAL_MODE }).
                         then( res => res.json() );

        if( (response.logged_out ?? false) === true) {
            await gotoLogin();
        } else {
            horriblyWrong();
        }
    };

    let loggedIn = false;
    requireLogin(async () => {
        loggedIn = true;
        await quiz.fetchQuestion();
        await quiz.fetchInfo();
    });
</script>

<style>
div.error {
    color: red;
}
div.correct {
    color: green;
}
div.incorrect {
    color: red;
}
span.error-header {
    font-size: 120%;
    font-weight: bold;
}
</style>


{#if loggedIn}
    <div class="row">
        <div class="twelve columns">
            <br>
        </div>
    </div>
    <div class="row">
        <div class="six columns">
            <h1>Quiz!</h1>
        </div>
        <div class="six columns">
            <div style="text-align: right">
                <button value="logout" on:click="{doLogout}">Logout</button> <br>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="three columns">
            &nbsp;
        </div>
        <div class="six columns">
            {#if quizErrorHandler.hasError}
                <div class="error">
                    <span class="error-header">Error!</span>
                    <br><br>
                    <ul>
                        {#each quizErrorHandler.errors as error}
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
    {#if quiz.didFetchQuestion}
        <div class="row" style="margin-bottom: 20px;">
            <div class="six columns" class:correct="{quiz.lastAnswerCorrect}" class:incorrect="{!quiz.lastAnswerCorrect}">
                {#if quiz.hasAnswered}
                    {#if quiz.lastAnswerCorrect}
                        Good answer!
                    {:else}
                        Your answer's bad, and you should feel bad!
                    {/if}
                {:else}
                    &nbsp;
                {/if}
            </div>
            <div class="three columns" class:correct="{quiz.correct>0}">
                Number of correct: {quiz.correct}
            </div>
            <div class="three columns" class:incorrect="{quiz.incorrect>0}">
                Number of incorrect: {quiz.incorrect}
            </div>
        </div>
        {#if quiz.hasQuestion}
            <form on:submit|preventDefault={checkAnswer}>
                <div class="row">
                    <div class="six columns">
                        <h5>{quiz.question}</h5>
                    </div>
                    <div class="six columns">
                        <label for="answer">Answer:</label><br>
                        <input class="u-full-width" type="text" id="answer" name="answer" bind:value="{quiz.answer}"><br>
                    </div>
                </div>
                <div class="row">
                    <div class="six columns">
                        &nbsp;
                    </div>
                    <div class="six columns">
                        <button class:button-primary="{!quiz.awaitingQuestion}" type="submit" value="checkAnswer" disabled="{quiz.awaitingQuestion}">Check Answer</button> <br>
                    </div>
                </div>
            </form>
        {:else}
            <div class="row">
                <div class="six columns">
                    <h5>There appears to be no more questions in this quiz.</h5>
                </div>
                <div class="three columns">
                    <div style="text-align: right">
                        <button class="button-primary" value="logout" on:click="{doLogout}">Logout</button> <br>
                    </div>
                </div>
                <div class="three columns">
                    &nbsp;
                </div>
            </div>
        {/if}
    {/if}
{/if}
