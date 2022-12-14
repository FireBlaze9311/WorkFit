type LoginData = {useremail: string, password: string};

export default function login(loginData: LoginData) {
    return new Promise<{ token?: string, error?: string }>((resolve, reject) => {

        const raw = JSON.stringify(loginData);

        fetch("https://etazeta.dev/WorkFit/WorkFit/dbif/login.php", {
            method: "POST",
            body: raw,
            redirect: "follow"
        })
            .then(response => response.json())
            .then(result => resolve(result))
            .catch(error => reject(error));
    });
}
