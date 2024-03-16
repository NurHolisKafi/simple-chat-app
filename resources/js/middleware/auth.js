const isLoggedIn = (next, auth) => {
    if (!auth) {
        next({ name: "login" });
    }
    next();
};

const CanLoggedIn = (next, auth) => {
    if (auth) {
        next({ name: "home" });
    }
    next();
};

export { isLoggedIn, CanLoggedIn };
