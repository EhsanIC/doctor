import { appendFile, mkdir } from "node:fs/promises";
import path from "node:path";
import { NextResponse } from "next/server";

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const logsDirectory = path.join(process.cwd(), "logs");
    const logFile = path.join(logsDirectory, "auth-debug.log");
    const entry = `${JSON.stringify({
      timestamp: new Date().toISOString(),
      ...body,
    })}\n`;

    await mkdir(logsDirectory, { recursive: true });
    await appendFile(logFile, entry, "utf8");

    return NextResponse.json({ ok: true });
  } catch {
    return NextResponse.json({ ok: false }, { status: 400 });
  }
}
