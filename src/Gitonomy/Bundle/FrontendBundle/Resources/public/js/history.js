function history_graph(commits) {
    var positions = {},
        matrix    = [],
        position,
        drawn     = [],
        link,
        links = []
    ;

    // Preparation
    commits.forEach(function (commit, i) {
        commits[i].position = i;
        commits[i].x = -1;
        commits[i].y = i;
        commits[i].children = [];

        positions[commit.hash] = i;
        matrix[i] = [];
    });

    // Children computing
    commits.forEach(function (commit, i) {
        commit.parents.forEach(function (parent) {
            if (positions[parent] == undefined) {
                return;
            }

            position = positions[parent];
            commits[position].children.push(commit.hash);
        });
    });

    // Weight computing
    commits.forEach(function (commit, i) {
        computeWeight(commit.hash);
    });

    // Draw
    commits.forEach(function (commit, i) {
        matrix_draw(i);
    });

    function computeWeight(hash) {
        if (positions[hash] == undefined) {
            return 1;
        }

        if (commits[positions[hash]].weight != undefined) {
            return commits[positions[hash]].weight;
        }

        var weight = 1;
        commits[positions[hash]].parents.forEach(function (parent) {
            weight = Math.max(weight, computeWeight(parent) + 1);
        });

        commits[positions[hash]].weight = weight;

        return weight;
    };

    function matrix_hashHeight(position, hash) {
        var i = 0;
        while (matrix[position][i] != hash && matrix[position][i] != undefined) {
            i++;
        }

        matrix[position][i] = hash;

        return i;
    };

    function matrix_draw(position) {
        drawn[position] = true;
        var commit = commits[position];
        var x = matrix_hashHeight(position, commit.hash);
        commit.x = x;

        var parents = commit.parents;

        parents.sort(function (left, right) {
            var leftWeight  = commits[positions[left]]  == undefined ? 1 : commits[positions[left]].weight;
            var rightWeight = commits[positions[right]] == undefined ? 1 : commits[positions[right]].weight;

            return rightWeight - leftWeight;
        });

        parents.forEach(function (parent) {
            if (positions[parent] == undefined) {
                return;
            }

            if (drawn[positions[parent]] == undefined) {
                matrix_draw(positions[parent]);
            }

            matrix_connect(commit.hash, parent);
        });
    };

    function matrix_connect(from, to) {
        if (positions[from] == undefined || positions[to] == undefined) {
            return;
        }

        var fromX = commits[positions[from]].x;
        var fromY = commits[positions[from]].y;
        var toX   = commits[positions[to]].x;
        var toY   = commits[positions[to]].y;
        var x,y;

        for (y = fromY; y < toY - 1; y++) {
            x = matrix_hashHeight(y + 1, to);
            links.push([
                {x: fromX, y: y},
                {x: x, y: y + 1}
            ]);
            fromX = x;
        }

        links.push([
            {x: fromX, y: y},
            {x: toX,   y: y + 1}
        ]);
    };

    return {
        nodes: commits,
        links: links
    };
}
